<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('jamaah_id');
            $table->unsignedBigInteger('invoice_id')->nullable();

            // ===============================
            // DATA PEMBAYARAN
            // ===============================
            $table->enum('metode', ['transfer', 'cash', 'kantor', 'gateway']);
            $table->dateTime('tanggal_bayar');

            $table->bigInteger('jumlah');

            $table->text('keterangan')->nullable();
            $table->string('bukti_transfer')->nullable();

            $table->enum('status', ['pending', 'valid', 'ditolak'])
                  ->default('pending');

            // ===============================
            // VALIDASI
            // ===============================
            $table->unsignedBigInteger('validated_by')->nullable();
            $table->dateTime('validated_at')->nullable();

            // ===============================
            // AUDIT FLAGS (SCHEMA LAMA)
            // ===============================
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_correction')->default(false);
            $table->unsignedBigInteger('corrected_from')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->dateTime('edited_at')->nullable();

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->dateTime('rejected_at')->nullable();

            // ===============================
            // TIMESTAMP
            // ===============================
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // ===============================
            // INDEX
            // ===============================
            $table->index('jamaah_id');
            $table->index('invoice_id');
            $table->index('status');
            $table->index('tanggal_bayar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
