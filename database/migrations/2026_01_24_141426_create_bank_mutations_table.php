<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_mutations', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // DATA MUTASI
            // ===============================
            $table->dateTime('tanggal');
            $table->text('deskripsi')->nullable();
            $table->bigInteger('nominal');

            $table->enum('jenis', ['debit', 'kredit']);

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('cocok_ke_payment_id')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->dateTime('created_at')->useCurrent();

            // ===============================
            // INDEX
            // ===============================
            $table->index('cocok_ke_payment_id');
            $table->index('tanggal');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_mutations');
    }
};
