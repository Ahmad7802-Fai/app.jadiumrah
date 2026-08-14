<?php

namespace App\Services\Ticketing;

use App\Models\TicketRefund;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketRefundApprovalService
{
    public function approve(TicketRefund $refund, int $userId): void
    {
        DB::transaction(function () use ($refund, $userId) {

            $refund = TicketRefund::lockForUpdate()
                ->findOrFail($refund->id);

            if ($refund->approval_status !== 'PENDING') {
                throw new Exception('Refund sudah diproses.');
            }

            // 🔒 LOCK INVOICE UNTUK SAFETY
            $invoice = $refund->invoice()
                ->lockForUpdate()
                ->firstOrFail();

            $approvedRefund = (int) $invoice->refunds()
                ->where('approval_status', 'APPROVED')
                ->sum('amount');

            $remainingRefundable = max(
                0,
                (int) $invoice->paid_amount - $approvedRefund
            );

            if ((int) $refund->amount > $remainingRefundable) {
                throw new Exception(
                    'Jumlah refund melebihi pembayaran yang tersedia.'
                );
            }

            $refund->approval_status = 'APPROVED';
            $refund->status          = 'REFUNDED';
            $refund->approved_by     = $userId;
            $refund->approved_at     = now();
            $refund->save();

            app(TicketInvoiceCalculator::class)
                ->recalculate($invoice);
        });
    }

    public function reject(TicketRefund $refund, int $userId): void
    {
        DB::transaction(function () use ($refund, $userId) {

            $refund = TicketRefund::lockForUpdate()
                ->findOrFail($refund->id);

            if ($refund->approval_status !== 'PENDING') {
                throw new Exception('Refund sudah diproses.');
            }

            $refund->approval_status = 'REJECTED';
            $refund->status          = 'REJECTED';
            $refund->approved_by     = $userId;
            $refund->approved_at     = now();

            $refund->save();
        });
    }
}
