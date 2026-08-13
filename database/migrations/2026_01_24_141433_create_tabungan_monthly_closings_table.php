<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabungan_monthly_closings', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // PERIODE
            // ===============================
            $table->unsignedTinyInteger('bulan'); // 1–12
            $table->unsignedSmallInteger('tahun');

            // ===============================
            // REKAP SALDO
            // ===============================
            $table->bigInteger('total_saldo_awal')->default(0);
            $table->bigInteger('total_topup')->default(0);
            $table->bigInteger('total_debit')->default(0);
            $table->bigInteger('total_saldo_akhir')->default(0);

            // ===============================
            // STATUS CLOSING
            // ===============================
            $table->boolean('is_final')->default(false);

            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();

            $table->dateTime('opened_at')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();

            // ===============================
            // AUDIT
            // ===============================
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // ===============================
            // UNIQUE KEY (SCHEMA LAMA)
            // ===============================
            $table->unique(['bulan', 'tahun'], 'uniq_tabungan_bulan_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_monthly_closings');
    }
};
