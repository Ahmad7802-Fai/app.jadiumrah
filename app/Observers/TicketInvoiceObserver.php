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


// namespace App\Observers;

// use App\Models\TicketInvoice;
// use App\Services\Ticketing\TicketAuditService;
// use Illuminate\Support\Facades\DB;

// class TicketInvoiceObserver
// {
//     public function updated(TicketInvoice $invoice): void
//     {
//         if (!$invoice->wasChanged('status')) {
//             return;
//         }

//         DB::transaction(function () use ($invoice) {

//             $pnr = $invoice->pnr()
//                 ->lockForUpdate()
//                 ->first();

//             if (!$pnr) {
//                 return;
//             }

//             /* ======================================================
//              | AUTO ISSUE PNR
//              | CONFIRMED → ISSUED
//              ====================================================== */
//             if (
//                 $invoice->status === 'PAID' &&
//                 $pnr->status === 'CONFIRMED'
//             ) {
//                 $before = $pnr->getOriginal();

//                 $pnr->update([
//                     'status' => 'ISSUED',
//                 ]);

//                 TicketAuditService::log(
//                     'PNR',
//                     $pnr->id,
//                     'PNR_ISSUED',
//                     $before,
//                     $pnr->fresh()->toArray()
//                 );

//                 return;
//             }

//             /* ======================================================
//              | AUTO CANCEL PNR (FULL REFUND)
//              | ISSUED / CONFIRMED → CANCELLED
//              ====================================================== */
//             if (
//                 $invoice->paid_amount === 0 &&
//                 in_array($pnr->status, ['CONFIRMED', 'ISSUED'])
//             ) {
//                 $before = $pnr->getOriginal();

//                 $pnr->update([
//                     'status' => 'CANCELLED',
//                 ]);

//                 TicketAuditService::log(
//                     'PNR',
//                     $pnr->id,
//                     'PNR_CANCELLED',
//                     $before,
//                     $pnr->fresh()->toArray()
//                 );
//             }
//         });
//     }
// }

// namespace App\Observers;

// use App\Models\TicketInvoice;
// use App\Services\Ticketing\TicketAuditService;
// use Illuminate\Support\Facades\DB;

// class TicketInvoiceObserver
// {
//     /**
//      * AUTO ISSUE PNR SAAT INVOICE PAID
//      */
//     public function updated(TicketInvoice $invoice): void
//     {
//         // ✅ HANYA JIKA STATUS BERUBAH KE PAID
//         if (
//             $invoice->wasChanged('status') &&
//             $invoice->status === 'PAID'
//         ) {
//             DB::transaction(function () use ($invoice) {

//                 $pnr = $invoice->pnr()
//                     ->lockForUpdate()
//                     ->first();

//                 if (!$pnr) return;
//                 if ($pnr->status === 'ISSUED') return;
//                 if ($pnr->status !== 'CONFIRMED') return;

//                 $before = $pnr->getOriginal();

//                 $pnr->update([
//                     'status' => 'ISSUED',
//                 ]);

//                 // ✅ AUDIT WAJIB
//                 TicketAuditService::log(
//                     'PNR',
//                     $pnr->id,
//                     'PNR_ISSUED',
//                     $before,
//                     $pnr->fresh()->toArray()
//                 );
//             });
//         }
//     }
// }
