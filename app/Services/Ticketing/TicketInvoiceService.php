<?php

namespace App\Services\Ticketing;

use App\Models\TicketInvoice;
use App\Models\TicketInvoiceItem;
use App\Models\TicketPnr;
use Illuminate\Support\Facades\DB;
use Exception;

class TicketInvoiceService
{
    /* ======================================================
     | CREATE INVOICE FROM PNR
     |
     | RULES (FINAL):
     | - 1 PNR = 1 Invoice
     | - Invoice dibuat SETELAH PNR CONFIRMED
     | - Total invoice = total_fare PNR (STATIC SNAPSHOT)
     | - Payment & Refund TIDAK di-handle di sini
     |   (Observer yang urus)
     ====================================================== */
    public function createFromPnr(int $pnrId): TicketInvoice
    {
        return DB::transaction(function () use ($pnrId) {

            /* ==================================================
             | LOCK PNR
             ================================================== */
            /** @var TicketPnr $pnr */
            $pnr = TicketPnr::lockForUpdate()
                ->findOrFail($pnrId);

            /* ==================================================
             | GUARDS (BUSINESS RULE)
             ================================================== */
            if ($pnr->status !== 'CONFIRMED') {
                throw new Exception(
                    'PNR harus berstatus CONFIRMED sebelum dibuat invoice.'
                );
            }

            if ($pnr->invoices()->exists()) {
                throw new Exception(
                    'Invoice untuk PNR ini sudah ada.'
                );
            }

            /* ==================================================
             | CREATE INVOICE (STATIC FINANCIAL SNAPSHOT)
             ================================================== */
            $invoice = TicketInvoice::create([
                'invoice_number'  => $this->generateInvoiceNumber(),
                'pnr_id'          => $pnr->id,
                'total_amount'    => $pnr->total_fare,
                'paid_amount'     => 0,
                'refunded_amount' => 0,
                'status'          => 'UNPAID',
                'created_by'      => auth()->id(),
            ]);

            /* ==================================================
             | INVOICE ITEM
             | (SINGLE LINE – BASE FARE)
             ================================================== */
            // $routeText = $pnr->routes
            //     ->map(fn ($r) => $r->origin . '–' . $r->destination)
            //     ->unique()
            //     ->implode('–');

            // /* ==================================================
            // | INVOICE ITEM (SINGLE LINE, RICH DESCRIPTION)
            // ================================================== */
            // TicketInvoiceItem::create([
            //     'ticket_invoice_id' => $invoice->id,
            //     'description'       =>
            //         'Tiket Pesawat – ' .
            //         $pnr->airline_name . ' ' . $pnr->airline_code .
            //         ' (' . $pnr->airline_class . ')' .
            //         ($routeText ? ' | ' . $routeText : '') .
            //         ' | ' . $pnr->pax . ' Pax',
            //     'qty'               => $pnr->pax,
            //     'unit_price'        => $pnr->fare_per_pax,
            //     'subtotal'          => $pnr->total_fare,
            // ]);

            TicketInvoiceItem::create([
                'ticket_invoice_id' => $invoice->id,
                'description'       => sprintf(
                    'Tiket Pesawat – %s %s (%s) – %d Pax',
                    $pnr->airline_name,
                    $pnr->airline_code,
                    $pnr->airline_class,
                    $pnr->pax
                ),
                'qty'               => $pnr->pax,
                'unit_price'        => $pnr->fare_per_pax,
                'subtotal'          => $pnr->total_fare,
            ]);

            /* ==================================================
             | AUDIT LOG
             ================================================== */
            TicketAuditService::log(
                'INVOICE',
                $invoice->id,
                'INVOICE_CREATED',
                null,
                $invoice->fresh()->toArray()
            );

            return $invoice;
        });
    }

    /* ======================================================
     | INVOICE NUMBER GENERATOR
     | FORMAT: INV-YYYY-000001
     ====================================================== */
    protected function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');

        $last = TicketInvoice::where(
                'invoice_number',
                'like',
                "INV-{$year}-%"
            )
            ->orderBy('invoice_number', 'desc')
            ->value('invoice_number');

        $next = 1;

        if ($last) {
            $next = (int) substr($last, -6) + 1;
        }

        return sprintf('INV-%s-%06d', $year, $next);
    }
}

// namespace App\Services\Ticketing;

// use App\Models\TicketInvoice;
// use App\Models\TicketInvoiceItem;
// use App\Models\TicketPnr;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class TicketInvoiceService
// {
//     /* ======================================================
//      | CREATE INVOICE FROM PNR
//      | RULES:
//      | - 1 PNR = 1 Invoice
//      | - Invoice dibuat setelah PNR CONFIRMED
//      | - TOTAL diambil dari total_fare PNR (STATIC)
//      ====================================================== */
//     public function createFromPnr(int $pnrId): TicketInvoice
//     {
//         return DB::transaction(function () use ($pnrId) {

//             /** @var TicketPnr $pnr */
//             $pnr = TicketPnr::where('id', $pnrId)
//                 ->lockForUpdate()
//                 ->firstOrFail();

//             /* ===============================
//              | GUARDS
//              =============================== */
//             if ($pnr->status !== 'CONFIRMED') {
//                 throw new Exception('PNR harus CONFIRMED sebelum dibuat invoice.');
//             }

//             if ($pnr->invoices()->exists()) {
//                 throw new Exception('Invoice untuk PNR ini sudah ada.');
//             }

//             /* ===============================
//              | CREATE INVOICE
//              =============================== */
//             $invoice = TicketInvoice::create([
//                 'invoice_number' => $this->generateInvoiceNumber(),
//                 'pnr_id'         => $pnr->id,
//                 'total_amount'   => $pnr->total_fare,
//                 'paid_amount'    => 0,
//                 'refunded_amount'=> 0,
//                 'status'         => 'UNPAID',
//                 'created_by'     => auth()->id(),
//             ]);

//             /* ===============================
//              | INVOICE ITEM
//              | (1 BARIS – BASE FARE)
//              =============================== */
//             TicketInvoiceItem::create([
//                 'ticket_invoice_id' => $invoice->id,
//                 'description'       => "Ticket Fare ({$pnr->pax} Pax)",
//                 'qty'               => $pnr->pax,
//                 'unit_price'        => $pnr->fare_per_pax,
//                 'subtotal'          => $pnr->total_fare,
//             ]);

//             /* ===============================
//              | AUDIT LOG
//              =============================== */
//             TicketAuditService::log(
//                 'INVOICE',
//                 $invoice->id,
//                 'INVOICE_CREATED',
//                 null,
//                 $invoice->fresh()->toArray()
//             );

//             return $invoice;
//         });
//     }

//     /* ======================================================
//      | RECALCULATE INVOICE FINANCIAL
//      | Dipanggil setelah:
//      | - Payment dibuat
//      | - Refund dibuat
//      ====================================================== */
//     public function recalculate(TicketInvoice $invoice): void
//     {
//         DB::transaction(function () use ($invoice) {

//             $invoice = TicketInvoice::where('id', $invoice->id)
//                 ->lockForUpdate()
//                 ->first();

//             $paid = $invoice->payments()
//                 ->where('status', 'VALID')
//                 ->sum('amount');

//             $refunded = $invoice->refunds()
//                 ->where('status', 'REFUNDED')
//                 ->sum('amount');

//             $netPaid = max(0, $paid - $refunded);

//             /* ===============================
//              | STATUS LOGIC
//              =============================== */
//             if ($netPaid <= 0) {
//                 $status = 'UNPAID';
//             } elseif ($netPaid < $invoice->total_amount) {
//                 $status = 'PARTIAL';
//             } else {
//                 $status = 'PAID';
//             }

//             $before = $invoice->getOriginal();

//             $invoice->update([
//                 'paid_amount'     => $netPaid,
//                 'refunded_amount' => $refunded,
//                 'status'          => $status,
//             ]);

//             TicketAuditService::log(
//                 'INVOICE',
//                 $invoice->id,
//                 'INVOICE_RECALCULATED',
//                 $before,
//                 $invoice->fresh()->toArray()
//             );
//         });
//     }

//     /* ======================================================
//      | INVOICE NUMBER GENERATOR
//      | FORMAT: INV-YYYY-000001
//      ====================================================== */
//     protected function generateInvoiceNumber(): string
//     {
//         $year = now()->format('Y');

//         $last = TicketInvoice::where('invoice_number', 'like', "INV-{$year}-%")
//             ->orderBy('invoice_number', 'desc')
//             ->value('invoice_number');

//         $next = 1;

//         if ($last) {
//             $next = (int) substr($last, -6) + 1;
//         }

//         return sprintf('INV-%s-%06d', $year, $next);
//     }
// }
