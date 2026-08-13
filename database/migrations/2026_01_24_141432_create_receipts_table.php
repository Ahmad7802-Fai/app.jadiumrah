<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('jamaah_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();

            // ===============================
            // DATA KWITANSI
            // ===============================
            $table->string('nomor_kwitansi', 50)->unique();
            $table->date('tanggal');
            $table->bigInteger('jumlah');

            $table->string('wa_tujuan', 20)->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('jamaah_id');
            $table->index('payment_id');
            $table->index('invoice_id');
            $table->index('dibuat_oleh');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
