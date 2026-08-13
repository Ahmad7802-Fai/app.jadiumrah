<?php

namespace App\Services\Ticketing;

use App\Models\{TicketInvoice, TicketRefund};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class TicketRefundService
{
    /* ======================================================
     | REQUEST REFUND (MAKER)
     ====================================================== */
    public function request(
        int $invoiceId,
        int $amount,
        ?string $reason = null
    ): TicketRefund {
        return DB::transaction(function () use ($invoiceId, $amount, $reason) {

            $invoice = TicketInvoice::lockForUpdate()
                ->findOrFail($invoiceId);

            if (!in_array($invoice->status, ['PAID', 'PARTIAL'])) {
                throw new Exception('Invoice tidak bisa direfund.');
            }

            if ($amount <= 0) {
                throw new Exception('Jumlah refund tidak valid.');
            }

            if ($amount > $invoice->paid_amount) {
                throw new Exception('Jumlah refund melebihi pembayaran.');
            }

            $refund = TicketRefund::create([
                'ticket_invoice_id' => $invoice->id,
                'amount'            => $amount,
                'reason'            => $reason,
                'status'            => 'REQUESTED',
                'approval_status'   => 'PENDING',
                'refunded_by'       => Auth::id(),
            ]);

            TicketAuditService::log(
                'REFUND',
                $refund->id,
                'REFUND_REQUESTED',
                null,
                $refund->toArray()
            );

            return $refund;
        });
    }

    /* ======================================================
     | APPROVE REFUND (CHECKER)
     ====================================================== */
    public function approve(TicketRefund $refund, int $approverId): void
    {
        DB::transaction(function () use ($refund, $approverId) {

            if ($refund->approval_status !== 'PENDING') {
                throw new Exception('Refund sudah diproses.');
            }

            $refund->update([
                'approval_status' => 'APPROVED',
                'approved_by'     => $approverId,
                'approved_at'     => now(),
            ]);

            TicketAuditService::log(
                'REFUND',
                $refund->id,
                'REFUND_APPROVED',
                null,
                $refund->toArray()
            );
        });
    }

    /* ======================================================
     | EXECUTE REFUND (FINANCE)
     | ❗ NO FINANCIAL CALC HERE
     ====================================================== */
    public function execute(TicketRefund $refund): void
    {
        DB::transaction(function () use ($refund) {

            if ($refund->approval_status !== 'APPROVED') {
                throw new Exception('Refund belum disetujui.');
            }

            if ($refund->status === 'EXECUTED') {
                return;
            }

            // 🔐 LOCK REFUND ROW
            $refund = TicketRefund::lockForUpdate()
                ->findOrFail($refund->id);

            $refund->update([
                'status'      => 'EXECUTED',
                'refunded_at' => now(),
            ]);

            TicketAuditService::log(
                'REFUND',
                $refund->id,
                'REFUND_EXECUTED',
                null,
                $refund->toArray()
            );

            /**
             * ❗ JANGAN update invoice di sini
             * Observer akan handle:
             * - refunded_amount
             * - status invoice
             * - status PNR
             */
        });
    }

    /* ======================================================
     | REJECT REFUND
     ====================================================== */
    public function reject(TicketRefund $refund, int $approverId): void
    {
        DB::transaction(function () use ($refund, $approverId) {

            if ($refund->approval_status !== 'PENDING') {
                return;
            }

            $refund->update([
                'approval_status' => 'REJECTED',
                'approved_by'     => $approverId,
                'approved_at'     => now(),
                'status'          => 'REJECTED',
            ]);

            TicketAuditService::log(
                'REFUND',
                $refund->id,
                'REFUND_REJECTED',
                null,
                $refund->toArray()
            );
        });
    }
}

// namespace App\Services\Ticketing;

// use App\Models\{TicketInvoice, TicketRefund};
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
// use Exception;

// class TicketRefundService
// {
//     /* ======================================================
//      | REQUEST REFUND (MAKER)
//      ====================================================== */
//     public function request(
//         int $invoiceId,
//         int $amount,
//         ?string $reason = null
//     ): TicketRefund {
//         return DB::transaction(function () use ($invoiceId, $amount, $reason) {

//             $invoice = TicketInvoice::lockForUpdate()
//                 ->findOrFail($invoiceId);

//             if (!in_array($invoice->status, ['PAID', 'PARTIAL'])) {
//                 throw new Exception('Invoice tidak bisa direfund.');
//             }

//             if ($amount <= 0) {
//                 throw new Exception('Jumlah refund tidak valid.');
//             }

//             if ($amount > $invoice->paid_amount) {
//                 throw new Exception('Jumlah refund melebihi pembayaran.');
//             }

//             $refund = TicketRefund::create([
//                 'ticket_invoice_id' => $invoice->id,
//                 'amount'            => $amount,
//                 'reason'            => $reason,
//                 'status'            => 'REQUESTED',
//                 'approval_status'   => 'PENDING',
//                 'refunded_by'       => Auth::id(),
//             ]);

//             TicketAuditService::log(
//                 'REFUND',
//                 $refund->id,
//                 'REFUND_REQUESTED',
//                 null,
//                 $refund->toArray()
//             );

//             return $refund;
//         });
//     }

//     /* ======================================================
//      | APPROVE REFUND (CHECKER)
//      ====================================================== */
//     public function approve(TicketRefund $refund, int $approverId): void
//     {
//         DB::transaction(function () use ($refund, $approverId) {

//             if ($refund->approval_status !== 'PENDING') {
//                 throw new Exception('Refund sudah diproses.');
//             }

//             $refund->update([
//                 'approval_status' => 'APPROVED',
//                 'approved_by'     => $approverId,
//                 'approved_at'     => now(),
//             ]);

//             TicketAuditService::log(
//                 'REFUND',
//                 $refund->id,
//                 'REFUND_APPROVED',
//                 null,
//                 $refund->toArray()
//             );
//         });
//     }

//     /* ======================================================
//      | EXECUTE REFUND (FINANCE)
//      ====================================================== */
//     public function execute(TicketRefund $refund): void
//     {
//         DB::transaction(function () use ($refund) {

//             if ($refund->approval_status !== 'APPROVED') {
//                 throw new Exception('Refund belum disetujui.');
//             }

//             if ($refund->status === 'EXECUTED') {
//                 return;
//             }

//             $invoice = TicketInvoice::lockForUpdate()
//                 ->findOrFail($refund->ticket_invoice_id);

//             /* ===============================
//              | MARK REFUND EXECUTED
//              =============================== */
//             $refund->update([
//                 'status'      => 'EXECUTED',
//                 'refunded_at' => now(),
//             ]);

//             /* ===============================
//              | RECALCULATE INVOICE (THE ONLY PLACE)
//              =============================== */
//             app(TicketInvoiceService::class)
//                 ->recalculate($invoice);

//             /* ===============================
//              | AUDIT
//              =============================== */
//             TicketAuditService::log(
//                 'REFUND',
//                 $refund->id,
//                 'REFUND_EXECUTED',
//                 null,
//                 [
//                     'refund_amount' => $refund->amount,
//                     'invoice_id'    => $invoice->id,
//                 ]
//             );
//         });
//     }

//     /* ======================================================
//      | REJECT REFUND
//      ====================================================== */
//     public function reject(TicketRefund $refund, int $approverId): void
//     {
//         if ($refund->approval_status !== 'PENDING') {
//             return;
//         }

//         $refund->update([
//             'approval_status' => 'REJECTED',
//             'approved_by'     => $approverId,
//             'approved_at'     => now(),
//             'status'          => 'REJECTED',
//         ]);

//         TicketAuditService::log(
//             'REFUND',
//             $refund->id,
//             'REFUND_REJECTED',
//             null,
//             $refund->toArray()
//         );
//     }
// }
