<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabungan_umrah', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('jamaah_id');
            $table->unsignedBigInteger('agent_id')->nullable();

            // ===============================
            // DATA TABUNGAN
            // ===============================
            $table->string('nomor_tabungan', 30)->nullable()->unique();
            $table->string('nama_tabungan', 100)->nullable();

            $table->bigInteger('target_nominal')->default(0);
            $table->bigInteger('saldo')->default(0);

            $table->string('status', 20)->default('ACTIVE');

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('jamaah_id', 'idx_tabungan_umrah_jamaah');
            $table->index('agent_id', 'idx_tabungan_umrah_agent');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_umrah');
    }
};
