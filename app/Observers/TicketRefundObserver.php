<?php

namespace App\Observers;

use App\Models\TicketRefund;
use App\Models\TicketAllocation;
use App\Services\Ticketing\TicketAuditService;
use Illuminate\Support\Facades\DB;

class TicketRefundObserver
{
    /**
     * Jalan hanya saat refund EXECUTED
     */
    public function updated(TicketRefund $refund): void
    {
        if (
            $refund->status !== 'EXECUTED' ||
            $refund->getOriginal('status') === 'EXECUTED'
        ) {
            return;
        }

        DB::transaction(function () use ($refund) {

            $invoice = $refund->invoice()
                ->lockForUpdate()
                ->first();

            if (!$invoice) return;

            $remaining = $refund->amount;

            /* =====================================
             | RELEASE ALLOCATION (FIFO)
             ===================================== */
            $allocations = TicketAllocation::where(
                    'ticket_invoice_id',
                    $invoice->id
                )
                ->where('status', 'ALLOCATED')
                ->orderBy('allocation_date')
                ->lockForUpdate()
                ->get();

            foreach ($allocations as $allocation) {

                if ($remaining <= 0) break;

                if ($allocation->allocated_amount <= $remaining) {
                    $remaining -= $allocation->allocated_amount;

                    $allocation->update([
                        'status' => 'RELEASED',
                    ]);
                } else {
                    // partial release
                    $allocation->update([
                        'allocated_amount' =>
                            $allocation->allocated_amount - $remaining,
                    ]);

                    TicketAllocation::create([
                        'pnr_id'            => $allocation->pnr_id,
                        'ticket_invoice_id' => $invoice->id,
                        'allocated_amount'  => $remaining,
                        'allocation_date'   => now(),
                        'status'            => 'RELEASED',
                    ]);

                    $remaining = 0;
                }
            }

            TicketAuditService::log(
                'REFUND',
                $refund->id,
                'REFUND_RELEASE_ALLOCATION',
                null,
                [
                    'invoice_id' => $invoice->id,
                    'amount'     => $refund->amount,
                ]
            );
        });
    }
}

// namespace App\Observers;

// use App\Models\TicketRefund;
// use Illuminate\Support\Facades\DB;

// class TicketRefundObserver
// {
//     /**
//      * Jalan hanya saat refund APPROVED
//      */
//     public function updated(TicketRefund $refund): void
//     {
//         if (
//             $refund->approval_status !== 'APPROVED' ||
//             $refund->getOriginal('approval_status') === 'APPROVED'
//         ) {
//             return;
//         }

//         DB::transaction(function () use ($refund) {

//             // 🔐 LOCK INVOICE
//             $invoice = $refund->invoice()
//                 ->lockForUpdate()
//                 ->firstOrFail();

//             /* ======================================================
//              | 1️⃣ HITUNG TOTAL REFUND (APPROVED)
//              ====================================================== */
//             $totalRefund = $invoice->refunds()
//                 ->where('approval_status', 'APPROVED')
//                 ->sum('amount');

//             $invoice->refunded_amount = $totalRefund;

//             /* ======================================================
//              | 2️⃣ HITUNG NET PAID
//              ====================================================== */
//             $netPaid = max(
//                 0,
//                 $invoice->paid_amount - $totalRefund
//             );

//             /* ======================================================
//              | 3️⃣ TENTUKAN STATUS INVOICE (FINAL)
//              ====================================================== */
//             if ($netPaid === 0) {
//                 $invoice->status = 'REFUNDED';
//             }
//             elseif ($netPaid < $invoice->total_amount) {
//                 $invoice->status = 'PARTIAL';
//             }
//             else {
//                 $invoice->status = 'PAID';
//             }

//             $invoice->save();

//             /**
//              * ❌ JANGAN UPDATE PNR DI SINI
//              * ✅ InvoiceObserver akan handle
//              */
//         });
//     }
// }

// namespace App\Observers;

// use App\Models\TicketRefund;
// use Illuminate\Support\Facades\DB;

// class TicketRefundObserver
// {
//     /**
//      * Trigger setelah refund di-update
//      * Fokus hanya saat approval_status berubah ke APPROVED
//      */
//     public function updated(TicketRefund $refund): void
//     {
//         // 🔒 HANYA JALAN JIKA BARU SAJA DI-APPROVE
//         if (
//             $refund->approval_status !== 'APPROVED' ||
//             $refund->getOriginal('approval_status') === 'APPROVED'
//         ) {
//             return;
//         }

//         DB::transaction(function () use ($refund) {

//             // 🔐 LOCK INVOICE (FINANCIAL SAFETY)
//             $invoice = $refund->invoice()
//                 ->lockForUpdate()
//                 ->firstOrFail();

//             /* ======================================================
//              | 1️⃣ TOTAL REFUND (APPROVED ONLY)
//              ====================================================== */
//             $totalRefund = $invoice->refunds()
//                 ->where('approval_status', 'APPROVED')
//                 ->sum('amount');

//             $invoice->refunded_amount = $totalRefund;

//             /* ======================================================
//              | 2️⃣ HITUNG NET PAID
//              ====================================================== */
//             $netPaid = max(
//                 0,
//                 $invoice->paid_amount - $totalRefund
//             );

//             // =========================
//             // STATUS INVOICE (FINAL & FIXED)
//             // =========================
//             if ($invoice->paid_amount <= 0) {
//                 $invoice->status = 'UNPAID';
//             }
//             elseif ($netPaid <= 0) {
//                 $invoice->status = 'REFUNDED';
//             }
//             elseif ($netPaid < $invoice->total_amount) {
//                 $invoice->status = 'PARTIAL';
//             }
//             elseif ($netPaid === $invoice->total_amount) {
//                 $invoice->status = 'PAID';
//             }


//             $invoice->save();

//             /* ======================================================
//              | 4️⃣ SYNC STATUS PNR (SAFE LOGIC)
//              ====================================================== */
//             if ($invoice->status === 'PAID') {
//                 // Paid penuh → tiket boleh issued
//                 $invoice->pnr()->update([
//                     'status' => 'ISSUED',
//                 ]);
//             } else {
//                 // Refund / Partial → tiket tetap CONFIRMED (tidak rollback ON_FLOW)
//                 $invoice->pnr()->update([
//                     'status' => 'CONFIRMED',
//                 ]);
//             }
//         });
//     }
// }
