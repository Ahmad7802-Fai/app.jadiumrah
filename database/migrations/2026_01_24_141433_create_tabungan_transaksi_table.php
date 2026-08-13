<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabungan_transaksi', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('tabungan_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();

            // ===============================
            // DATA TRANSAKSI
            // ===============================
            $table->string('jenis', 30); // debit / kredit / koreksi / dsb
            $table->bigInteger('amount');

            $table->bigInteger('saldo_sebelum')->default(0);
            $table->bigInteger('saldo_setelah')->default(0);

            // ===============================
            // REFERENSI DINAMIS (INVOICE / PAYMENT / DLL)
            // ===============================
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('keterangan')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->timestamp('created_at')->nullable();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('created_at', 'idx_tabungan_transaksi_created_at');
            $table->index(
                ['tabungan_id', 'created_at'],
                'idx_tabungan_created'
            );
            $table->index('reference_type');
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_transaksi');
    }
};
