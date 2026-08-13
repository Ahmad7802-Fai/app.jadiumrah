<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_allocations', function (Blueprint $table) {
            $table->foreignId('ticket_invoice_id')
                  ->nullable()
                  ->after('pnr_id')
                  ->constrained('ticket_invoices')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_allocations', function (Blueprint $table) {
            $table->dropForeign(['ticket_invoice_id']);
            $table->dropColumn('ticket_invoice_id');
        });
    }
};
