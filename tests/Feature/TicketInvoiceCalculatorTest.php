<?php

namespace Tests\Feature;

use App\Models\TicketInvoice;
use App\Services\Ticketing\TicketInvoiceCalculator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketInvoiceCalculatorTest extends TestCase
{
    use DatabaseTransactions;

    private int $sequence = 0;

    private function createInvoice(
        int $totalAmount = 1000000
    ): TicketInvoice {
        $this->sequence++;

        $suffix = strtoupper(
            bin2hex(random_bytes(6))
        );

        $pnrId = DB::table('ticket_pnrs')->insertGetId([
            'pnr_code' => 'TST-PNR-'.$suffix,
            'status' => 'ON_FLOW',
        ]);

        $invoiceId = DB::table('ticket_invoices')->insertGetId([
            'invoice_number' => 'TST-INV-'.$suffix,
            'pnr_id' => $pnrId,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'refunded_amount' => 0,
            'status' => 'UNPAID',
        ]);

        return TicketInvoice::query()->findOrFail(
            $invoiceId
        );
    }

    private function insertPayment(
        TicketInvoice $invoice,
        int $amount,
        string $status = 'VALID'
    ): void {
        DB::table('ticket_payments')->insert([
            'ticket_invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => $amount,
            'method' => 'CASH',
            'status' => $status,
        ]);
    }

    private function insertRefund(
        TicketInvoice $invoice,
        int $amount,
        string $approvalStatus
    ): void {
        DB::table('ticket_refunds')->insert([
            'ticket_invoice_id' => $invoice->id,
            'amount' => $amount,
            'status' => 'PARTIAL',
            'approval_status' => $approvalStatus,
        ]);
    }

    private function recalculate(
        TicketInvoice $invoice
    ): TicketInvoice {
        app(TicketInvoiceCalculator::class)
            ->recalculate($invoice);

        return $invoice->fresh();
    }

    public function test_valid_partial_payment_sets_partial_state(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            400000
        );

        $invoice = $this->recalculate($invoice);

        $this->assertSame(
            400000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            0,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }

    public function test_valid_full_payment_sets_paid_state(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            1000000
        );

        $invoice = $this->recalculate($invoice);

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            0,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            'PAID',
            $invoice->status
        );
    }

    public function test_non_valid_payment_is_not_counted(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            1000000,
            'VOID'
        );

        $invoice = $this->recalculate($invoice);

        $this->assertSame(
            0,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            0,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            'UNPAID',
            $invoice->status
        );
    }

    public function test_pending_refund_is_not_counted(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            1000000
        );

        $this->insertRefund(
            $invoice,
            250000,
            'PENDING'
        );

        $invoice = $this->recalculate($invoice);

        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            0,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            'PAID',
            $invoice->status
        );
    }

    public function test_approved_refund_is_counted_by_calculator(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            1000000
        );

        $this->insertRefund(
            $invoice,
            250000,
            'APPROVED'
        );

        $invoice = $this->recalculate($invoice);

        /*
         * paid_amount stores gross VALID payments.
         * Approved refunds are tracked separately.
         */
        $this->assertSame(
            1000000,
            (int) $invoice->paid_amount
        );

        $this->assertSame(
            250000,
            (int) $invoice->refunded_amount
        );

        $this->assertSame(
            'PARTIAL',
            $invoice->status
        );
    }

    public function test_approved_refund_keeps_net_and_outstanding_consistent(): void
    {
        $invoice = $this->createInvoice();

        $this->insertPayment(
            $invoice,
            1000000
        );

        $this->insertRefund(
            $invoice,
            250000,
            'APPROVED'
        );

        $invoice = $this->recalculate($invoice);

        /*
         * Financial invariant:
         *
         * gross received  = 1,000,000
         * approved refund =   250,000
         * net cash        =   750,000
         * outstanding     =   250,000
         *
         * This test intentionally verifies the public
         * model accessors, not only stored columns.
         */
        $this->assertSame(
            750000,
            (int) $invoice->net_paid
        );

        $this->assertSame(
            250000,
            (int) $invoice->outstanding_amount
        );
    }
}
