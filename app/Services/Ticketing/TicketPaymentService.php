<?php

namespace App\Services\Ticketing;

use App\Models\TicketInvoice;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketPaymentService
{
    /* ======================================================
     | CREATE PAYMENT
     |
     | RULES (FINAL):
     | - Payment = append only (tidak pernah update invoice langsung)
     | - InvoiceObserver = single source of truth
     | - Tidak ada hitung ulang di service
     ====================================================== */
    public function pay(
        TicketInvoice $invoice,
        int $amount,
        int $userId,
        string $method,
        ?string $bank = null,
        ?string $receipt = null
    ): void {
        DB::transaction(function () use (
            $invoice, $amount, $userId, $method, $bank, $receipt
        ) {

            /* ==================================================
             | LOCK INVOICE (ANTI DOUBLE PAYMENT)
             ================================================== */
            $invoice = TicketInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            /* ==================================================
             | VALIDATION
             ================================================== */
            if ($invoice->status === 'PAID') {
                throw new Exception('Invoice sudah lunas.');
            }

            if ($amount <= 0) {
                throw new Exception('Jumlah pembayaran tidak valid.');
            }

            if ($amount > $invoice->outstanding_amount) {
                throw new Exception('Jumlah pembayaran melebihi outstanding.');
            }

            /* ==================================================
             | CREATE PAYMENT (APPEND ONLY)
             ================================================== */
            $payment = $invoice->payments()->create([
                'payment_date' => now()->toDateString(),
                'amount'       => $amount,
                'method'       => $method,
                'bank'         => $bank,
                'receipt_file' => $receipt,
                'status'       => 'VALID',
                'created_by'   => $userId,
            ]);

            /* ==================================================
             | AUDIT LOG
             ================================================== */
            TicketAuditService::log(
                'PAYMENT',
                $payment->id,
                'PAYMENT_CREATED',
                null,
                $payment->toArray()
            );

            /**
             * ❗ PENTING:
             * - JANGAN update invoice di sini
             * - Observer akan otomatis jalan
             */
        });
    }
}

// namespace App\Services\Ticketing;

// use App\Models\TicketInvoice;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class TicketPaymentService
// {
//     public function pay(
//         TicketInvoice $invoice,
//         int $amount,
//         int $userId,
//         string $method,
//         ?string $bank = null,
//         ?string $receipt = null
//     ): void {
//         DB::transaction(function () use (
//             $invoice, $amount, $userId, $method, $bank, $receipt
//         ) {

//             /* ======================================================
//              | LOCK INVOICE (ANTI DOUBLE PAYMENT)
//              ====================================================== */
//             $invoice = TicketInvoice::where('id', $invoice->id)
//                 ->lockForUpdate()
//                 ->firstOrFail();

//             /* ======================================================
//              | VALIDATION
//              ====================================================== */
//             if ($invoice->status === 'PAID') {
//                 throw new Exception('Invoice sudah lunas.');
//             }

//             if ($amount <= 0) {
//                 throw new Exception('Jumlah pembayaran tidak valid.');
//             }

//             /* ======================================================
//              | CREATE PAYMENT (APPEND ONLY)
//              ====================================================== */
//             $payment = $invoice->payments()->create([
//                 'payment_date' => now()->toDateString(),
//                 'amount'       => $amount,
//                 'method'       => $method,
//                 'bank'         => $bank,
//                 'receipt_file' => $receipt,
//                 'status'       => 'VALID',
//                 'created_by'   => $userId,
//             ]);

//             /* ======================================================
//              | RECALCULATE INVOICE (SINGLE SOURCE OF TRUTH)
//              ====================================================== */
//             app(TicketInvoiceService::class)
//                 ->recalculate($invoice);

//             /* ======================================================
//              | AUDIT LOG
//              ====================================================== */
//             TicketAuditService::log(
//                 'PAYMENT',
//                 $payment->id,
//                 'PAYMENT_CREATED',
//                 null,
//                 $payment->toArray()
//             );
//         });
//     }
// }
