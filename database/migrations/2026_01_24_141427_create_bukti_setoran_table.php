<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bukti_setoran', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->string('nomor_bukti', 50)->unique();

            // relasi (FK nanti)
            $table->unsignedBigInteger('tabungan_transaksi_id')->unique();
            $table->unsignedInteger('jamaah_id');
            $table->unsignedBigInteger('tabungan_id');

            $table->bigInteger('nominal');
            $table->date('tanggal_setoran');

            $table->unsignedBigInteger('approved_by');
            $table->timestamp('approved_at');

            $table->string('hash', 64);
            $table->string('qr_path')->nullable();

            // schema lama: created_at timestamp nullable default current
            $table->timestamp('created_at')->useCurrent()->nullable();

            // index sesuai schema lama
            $table->index('jamaah_id');
            $table->index('tabungan_id');
            $table->index('approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_setoran');
    }
};
