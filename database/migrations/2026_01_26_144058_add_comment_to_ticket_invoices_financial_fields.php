<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE ticket_invoices
            MODIFY paid_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'Derived from ticket_payments',
            MODIFY refunded_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'Derived from approved ticket_refunds'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE ticket_invoices
            MODIFY paid_amount BIGINT NOT NULL DEFAULT 0,
            MODIFY refunded_amount BIGINT NOT NULL DEFAULT 0
        ");
    }
};
