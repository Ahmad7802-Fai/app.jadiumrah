<?php

namespace App\Services\Ticketing;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TicketReportService
{
    public function payments(string $from, string $to): Collection
    {
        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();

        return collect(DB::select(
            "
            SELECT
                i.invoice_number      AS invoice_number,
                pnr.pnr_code          AS pnr_code,
                p.payment_date        AS payment_date,
                p.amount              AS amount,
                i.status              AS invoice_status
            FROM ticket_payments p
            JOIN ticket_invoices i ON i.id = p.ticket_invoice_id
            JOIN ticket_pnrs pnr   ON pnr.id = i.pnr_id
            WHERE p.payment_date BETWEEN ? AND ?
            ORDER BY p.payment_date DESC
            ",
            [$from, $to]
        ));
    }

    public function refunds(string $from, string $to): Collection
    {
        $from = Carbon::parse($from)->startOfDay();
        $to   = Carbon::parse($to)->endOfDay();

        return collect(DB::select(
            "
            SELECT
                i.invoice_number      AS invoice_number,
                pnr.pnr_code          AS pnr_code,
                r.refunded_at         AS refunded_at,
                r.amount              AS amount,
                r.status              AS refund_status,
                r.reason              AS reason
            FROM ticket_refunds r
            JOIN ticket_invoices i ON i.id = r.ticket_invoice_id
            JOIN ticket_pnrs pnr   ON pnr.id = i.pnr_id
            WHERE r.refunded_at BETWEEN ? AND ?
            ORDER BY r.refunded_at DESC
            ",
            [$from, $to]
        ));
    }
}
