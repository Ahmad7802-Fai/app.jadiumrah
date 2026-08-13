<?php

namespace App\Services\Ticketing;

use App\Models\TicketInvoice;
use Illuminate\Support\Facades\DB;

class TicketInvoiceCalculator
{
    public function recalculate(TicketInvoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {

            $invoice = TicketInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            $paid = $invoice->payments()
                ->where('status', 'VALID')
                ->sum('amount');

            $refunded = $invoice->refunds()
                ->where('approval_status', 'APPROVED')
                ->sum('amount');

            $netPaid = max(0, $paid - $refunded);

            if ($netPaid <= 0) {
                $status = 'UNPAID';
            } elseif ($netPaid < $invoice->total_amount) {
                $status = 'PARTIAL';
            } else {
                $status = 'PAID';
            }

            $invoice->update([
                'paid_amount'     => $netPaid,
                'refunded_amount' => $refunded,
                'status'          => $status,
            ]);
        });
    }
}
