<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW v_ticket_pnr_financials AS
            SELECT
                p.id AS pnr_id,
                p.pnr_code,
                COALESCE(SUM(i.total_amount),0) AS total_invoice,
                COALESCE(SUM(i.paid_amount),0) AS total_paid,
                COALESCE(SUM(i.refunded_amount),0) AS total_refunded,
                (COALESCE(SUM(i.total_amount),0)
                 - COALESCE(SUM(i.paid_amount),0)
                 + COALESCE(SUM(i.refunded_amount),0)
                ) AS outstanding
            FROM ticket_pnrs p
            LEFT JOIN ticket_invoices i ON i.pnr_id = p.id
            GROUP BY p.id, p.pnr_code
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_ticket_pnr_financials");
    }
};
