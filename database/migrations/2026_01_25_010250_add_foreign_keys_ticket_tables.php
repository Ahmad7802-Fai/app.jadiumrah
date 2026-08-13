<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===============================
        // TICKET PNRS (PARENT)
        // ===============================
        Schema::table('ticket_pnrs', function (Blueprint $table) {
            $table->foreign('agent_id')
                ->references('id')->on('agents')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // ===============================
        // TICKET ALLOCATIONS
        // ===============================
        Schema::table('ticket_allocations', function (Blueprint $table) {
            $table->foreign('pnr_id')
                ->references('id')->on('ticket_pnrs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // TICKET INVOICES
        // ===============================
        Schema::table('ticket_invoices', function (Blueprint $table) {
            $table->foreign('pnr_id')
                ->references('id')->on('ticket_pnrs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // TICKET ROUTES
        // ===============================
        Schema::table('ticket_routes', function (Blueprint $table) {
            $table->foreign('pnr_id')
                ->references('id')->on('ticket_pnrs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // TICKET PAYMENTS
        // ===============================
        Schema::table('ticket_payments', function (Blueprint $table) {
            $table->foreign('ticket_invoice_id')
                ->references('id')->on('ticket_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_payments', fn (Blueprint $t) =>
            $t->dropForeign(['ticket_invoice_id'])
        );

        Schema::table('ticket_routes', fn (Blueprint $t) =>
            $t->dropForeign(['pnr_id'])
        );

        Schema::table('ticket_invoices', fn (Blueprint $t) =>
            $t->dropForeign(['pnr_id'])
        );

        Schema::table('ticket_allocations', fn (Blueprint $t) =>
            $t->dropForeign(['pnr_id'])
        );

        Schema::table('ticket_pnrs', function (Blueprint $t) {
            $t->dropForeign(['agent_id']);
            $t->dropForeign(['branch_id']);
            $t->dropForeign(['client_id']);
        });
    }
};

