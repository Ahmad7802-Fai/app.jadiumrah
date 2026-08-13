<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===============================
        // INVOICES
        // ===============================
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign('paket_id')
                ->references('id')->on('paket_master')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // ===============================
        // PAYMENTS
        // ===============================
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // atau nullOnDelete jika nullable
        });

        // ===============================
        // RECEIPTS
        // ===============================
        Schema::table('receipts', function (Blueprint $table) {
            $table->foreign('invoice_id')
                ->references('id')->on('invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('payment_id')
                ->references('id')->on('payments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // BANK MUTATIONS
        // ===============================
        Schema::table('bank_mutations', function (Blueprint $table) {
            $table->foreign('cocok_ke_payment_id')
                ->references('id')->on('payments')
                ->cascadeOnUpdate()
                ->nullOnDelete(); // audit-safe
        });
    }

    public function down(): void
    {
        Schema::table('bank_mutations', fn (Blueprint $t) =>
            $t->dropForeign(['cocok_ke_payment_id'])
        );

        Schema::table('receipts', function (Blueprint $t) {
            $t->dropForeign(['invoice_id']);
            $t->dropForeign(['payment_id']);
        });

        Schema::table('payments', function (Blueprint $t) {
            $t->dropForeign(['invoice_id']);
            $t->dropForeign(['jamaah_id']);
        });

        Schema::table('invoices', fn (Blueprint $t) =>
            $t->dropForeign(['paket_id'])
        );
    }
};

