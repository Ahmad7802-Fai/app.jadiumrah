<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        /**
         * ======================================================
         * LAYANAN ITEM → LAYANAN MASTER
         * ======================================================
         */
        Schema::table('layanan_item', function (Blueprint $table) {
            $table->foreign('id_layanan_master')
                ->references('id')
                ->on('layanan_master')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        /**
         * ======================================================
         * LAYANAN TRANSAKSI → CLIENTS
         * ======================================================
         */
        Schema::table('layanan_transaksi', function (Blueprint $table) {
            $table->foreign('id_client')
                ->references('id')
                ->on('clients')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        /**
         * ======================================================
         * LAYANAN TRANSAKSI ITEMS
         * ======================================================
         */
        Schema::table('layanan_transaksi_items', function (Blueprint $table) {
            $table->foreign('id_transaksi')
                ->references('id')
                ->on('layanan_transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_layanan_item')
                ->references('id')
                ->on('layanan_item')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        /**
         * ======================================================
         * LAYANAN INVOICES → LAYANAN TRANSAKSI
         * ======================================================
         */
        Schema::table('layanan_invoices', function (Blueprint $table) {
            $table->foreign('id_transaksi')
                ->references('id')
                ->on('layanan_transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        /**
         * ======================================================
         * LAYANAN PAYMENTS → LAYANAN INVOICES
         * ======================================================
         */
        Schema::table('layanan_payments', function (Blueprint $table) {
            $table->foreign('layanan_invoice_id')
                ->references('id')
                ->on('layanan_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        /**
         * ======================================================
         * 🔥 VENDOR PAYMENTS → LAYANAN ITEM (SET NULL SAFE)
         * ======================================================
         * PENTING:
         * - Kolom HARUS nullable sebelum FK
         * - Kalau tidak, MySQL akan simpan FK cacat
         */

        // 1️⃣ pastikan kolom nullable
        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('layanan_item_id')
                ->nullable()
                ->change();
        });

        // 2️⃣ baru tambahkan FK
        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->foreign('layanan_item_id')
                ->references('id')
                ->on('layanan_item')
                ->cascadeOnUpdate()
                ->nullOnDelete(); // audit-safe
        });
    }

    public function down(): void
    {
        /**
         * ======================================================
         * DROP FK (URUTAN TERBALIK)
         * ======================================================
         */

        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->dropForeign(['layanan_item_id']);
        });

        Schema::table('layanan_payments', function (Blueprint $table) {
            $table->dropForeign(['layanan_invoice_id']);
        });

        Schema::table('layanan_invoices', function (Blueprint $table) {
            $table->dropForeign(['id_transaksi']);
        });

        Schema::table('layanan_transaksi_items', function (Blueprint $table) {
            $table->dropForeign(['id_transaksi']);
            $table->dropForeign(['id_layanan_item']);
        });

        Schema::table('layanan_transaksi', function (Blueprint $table) {
            $table->dropForeign(['id_client']);
        });

        Schema::table('layanan_item', function (Blueprint $table) {
            $table->dropForeign(['id_layanan_master']);
        });
    }
};
