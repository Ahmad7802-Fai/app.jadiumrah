<?php

namespace Tests\Feature;

use App\Models\TicketInvoice;
use App\Models\TicketRefund;
use App\Services\Ticketing\TicketInvoiceCalculator;
use App\Services\Ticketing\TicketRefundApprovalService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketRefundApprovalFinancialSyncTest extends TestCase
{
    use DatabaseTransactions;

    private function createPaidInvoice(): TicketInvoice
    {
        $suffix = strtoupper(
            bin2hex(random_bytes(6))
        );

        $pnrId = DB::table('ticket_pnrs')->insertGetId([
            'pnr_code' => 'TST-RFD-'.$suffix,
            'status' => 'ON_FLOW',
        ]);

        $invoiceId = DB::table('ticket_invoices')->insertGetId([
            'invoice_number' => 'RFD-INV-'.$suffix,
            'pnr_id' => $pnrId,
            'total_amount' => 1000000,
            'paid_amount' => 0,
            'refunded_amount' => 0,
            'status' => 'UNPAID',
        ]);

        DB::table('ticket_payments')->insert([
            'ticket_invoice_id' => $invoiceId,
            'payment_date' => now()->toDateString(),
            'amount' => 1000000,
            'method' => 'CASH',
            'status' => 'VALID',
        ]);

        $invoice = TicketInvoice::query()
            ->findOrFail($invoiceId);

        app(TicketInvoiceCalculator::class)
            ->recalculate($invoice);

        return $invoice->fresh();
    }

    private function createPendingRefund(
        TicketInvoice $invoice,
        int $amount = 250000
    ): TicketRefund {
        $refundId = DB::table('ticket_refunds')
            ->insertGetId([
                'ticket_invoice_id' => $invoice->id,
                'amount' => $amount,
                'reason' => 'Test refund approval sync',
                'status' => 'REQUESTED',
                'approval_status' => 'PENDING',
            ]);

        return TicketRefund::query()
            ->findOrFail($refundId);
    }

    public function test_approving_refund_recalculates_invoice_financial_state(): void
    {
        $invoice = $this->createPaidInvoice();

        $this->assertSame(
            'PAID',
            $invoice->status
        );

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $refund = $this->createPendingRefund(
            $invoice
        );

        app(TicketRefundApprovalService::class)
            ->approve(
                $refund,
                1
            );

        $refund = $refund->fresh();
        $invoice = $invoice->fresh();

        $this->assertSame(
            'APPROVED',
            $refund->approval_status
        );

        $this->assertSame(
            'REFUNDED',
            $refund->status
        );

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            250000,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            750000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            250000,
            (int) $invoice->outstanding_amount
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }
    public function test_multiple_approved_refunds_are_accumulated(): void
    {
        $invoice = $this->createPaidInvoice();

        $first = $this->createPendingRefund(
            $invoice,
            250000
        );

        app(TicketRefundApprovalService::class)
            ->approve($first, 1);

        $second = $this->createPendingRefund(
            $invoice,
            300000
        );

        app(TicketRefundApprovalService::class)
            ->approve($second, 1);

        $invoice = $invoice->fresh();

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            550000,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            450000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            550000,
            (int) $invoice->outstanding_amount
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }

    public function test_rejected_refund_does_not_change_invoice_financial_state(): void
    {
        $invoice = $this->createPaidInvoice();

        $refund = $this->createPendingRefund(
            $invoice,
            250000
        );

        app(TicketRefundApprovalService::class)
            ->reject($refund, 1);

        $refund = $refund->fresh();
        $invoice = $invoice->fresh();

        $this->assertSame(
            'REJECTED',
            $refund->approval_status
        );

        $this->assertSame(
            'REJECTED',
            $refund->status
        );

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            0,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            1000000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            0,
            (int) $invoice->outstanding_amount
        );

        $this->assertSame(
            'PAID',
            $invoice->status
        );
    }

    public function test_approval_rejects_refund_above_remaining_refundable_balance(): void
    {
        $invoice = $this->createPaidInvoice();

        $first = $this->createPendingRefund(
            $invoice,
            700000
        );

        $second = $this->createPendingRefund(
            $invoice,
            400000
        );

        app(TicketRefundApprovalService::class)
            ->approve($first, 1);

        $exception = null;

        try {
            app(TicketRefundApprovalService::class)
                ->approve($second, 1);
        } catch (\Exception $caught) {
            $exception = $caught;
        }

        $this->assertNotNull(
            $exception,
            'Approval seharusnya menolak refund yang melebihi saldo refundable.'
        );

        $this->assertSame(
            'Jumlah refund melebihi pembayaran yang tersedia.',
            $exception->getMessage()
        );

        $second = $second->fresh();
        $invoice = $invoice->fresh();

        $this->assertSame(
            'PENDING',
            $second->approval_status
        );

        $this->assertSame(
            'REQUESTED',
            $second->status
        );

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            700000,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            300000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            700000,
            (int) $invoice->outstanding_amount
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }

    public function test_stale_approve_cannot_process_an_already_approved_refund(): void
    {
        $invoice = $this->createPaidInvoice();

        $refund = $this->createPendingRefund(
            $invoice,
            250000
        );

        $firstView = TicketRefund::query()
            ->findOrFail($refund->id);

        $staleView = TicketRefund::query()
            ->findOrFail($refund->id);

        $service = app(
            TicketRefundApprovalService::class
        );

        $service->approve(
            $firstView,
            1
        );

        $exception = null;

        try {
            $service->approve(
                $staleView,
                1
            );
        } catch (\Exception $caught) {
            $exception = $caught;
        }

        $this->assertNotNull(
            $exception,
            'Stale approval harus ditolak setelah refund sudah diproses.'
        );

        $this->assertSame(
            'Refund sudah diproses.',
            $exception->getMessage()
        );

        $refund = $refund->fresh();

        $this->assertSame(
            'APPROVED',
            $refund->approval_status
        );

        $this->assertSame(
            'REFUNDED',
            $refund->status
        );
    }

    public function test_stale_reject_cannot_reverse_an_approved_refund(): void
    {
        $invoice = $this->createPaidInvoice();

        $refund = $this->createPendingRefund(
            $invoice,
            250000
        );

        $approveView = TicketRefund::query()
            ->findOrFail($refund->id);

        $staleRejectView = TicketRefund::query()
            ->findOrFail($refund->id);

        $service = app(
            TicketRefundApprovalService::class
        );

        $service->approve(
            $approveView,
            1
        );

        $exception = null;

        try {
            $service->reject(
                $staleRejectView,
                1
            );
        } catch (\Exception $caught) {
            $exception = $caught;
        }

        $this->assertNotNull(
            $exception,
            'Stale rejection harus ditolak setelah refund sudah approved.'
        );

        $this->assertSame(
            'Refund sudah diproses.',
            $exception->getMessage()
        );

        $refund = $refund->fresh();
        $invoice = $invoice->fresh();

        $this->assertSame(
            'APPROVED',
            $refund->approval_status
        );

        $this->assertSame(
            'REFUNDED',
            $refund->status
        );

        $this->assertSame(
            250000,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            750000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }

}
