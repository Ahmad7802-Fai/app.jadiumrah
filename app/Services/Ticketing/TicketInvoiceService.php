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
