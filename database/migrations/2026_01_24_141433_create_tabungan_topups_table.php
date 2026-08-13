<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tabungan_topups', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('tabungan_id');
            $table->unsignedBigInteger('jamaah_id');
            $table->unsignedBigInteger('verified_by')->nullable();

            // ===============================
            // DATA TOPUP
            // ===============================
            $table->bigInteger('amount');
            $table->date('transfer_date')->nullable();

            $table->string('bank_sender', 100)->nullable();
            $table->string('bank_receiver', 100)->nullable();

            $table->string('proof_file')->nullable();

            $table->string('status', 20)->default('PENDING');

            $table->string('wa_token', 36)->nullable()->unique();
            $table->text('admin_note')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('wa_verified_at')->nullable();
            $table->timestamp('wa_rejected_at')->nullable();
            $table->timestamp('created_at')->nullable();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('tabungan_id', 'idx_tabungan_topups_tabungan');
            $table->index('jamaah_id', 'idx_tabungan_topups_jamaah');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_topups');
    }
};
