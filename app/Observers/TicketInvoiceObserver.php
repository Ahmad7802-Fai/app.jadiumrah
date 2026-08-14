<?php

namespace App\Observers;

use App\Models\TicketInvoice;
use App\Services\Ticketing\TicketAuditService;
use Illuminate\Support\Facades\DB;

class TicketInvoiceObserver
{
    public function updated(TicketInvoice $invoice): void
    {
        // jalan hanya jika status berubah
        if (!$invoice->wasChanged('status')) {
            return;
        }

        DB::transaction(function () use ($invoice) {

            $pnr = $invoice->pnr()
                ->lockForUpdate()
                ->first();

            if (!$pnr) {
                return;
            }

            /* ======================================================
             | AUTO ISSUE
             | CONFIRMED → ISSUED
             ====================================================== */
            if (
                $invoice->status === 'PAID' &&
                $pnr->status === 'CONFIRMED'
            ) {
                $before = $pnr->getOriginal();

                $pnr->update([
                    'status' => 'ISSUED',
                ]);

                TicketAuditService::log(
                    'PNR',
                    $pnr->id,
                    'PNR_ISSUED',
                    $before,
                    $pnr->fresh()->toArray()
                );

                return;
            }

            /* ======================================================
             | AUTO CANCEL (FULL REFUND ONLY)
             | ISSUED / CONFIRMED → CANCELLED
             ====================================================== */
            if (
                $invoice->status === 'REFUNDED' &&
                in_array($pnr->status, ['CONFIRMED', 'ISSUED'])
            ) {
                $before = $pnr->getOriginal();

                $pnr->update([
                    'status' => 'CANCELLED',
                ]);

                TicketAuditService::log(
                    'PNR',
                    $pnr->id,
                    'PNR_CANCELLED',
                    $before,
                    $pnr->fresh()->toArray()
                );
            }
        });
    }
}
