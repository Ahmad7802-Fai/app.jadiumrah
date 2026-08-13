<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment (Laravel pakai bigint? → aman, tapi kita jaga int)

            // relasi (FK nanti)
            $table->unsignedInteger('paket_id');

            $table->string('kategori', 100);
            $table->bigInteger('jumlah');

            $table->date('tanggal');

            $table->text('catatan')->nullable();
            $table->string('bukti')->nullable();

            $table->unsignedInteger('dibuat_oleh')->nullable();

            // timestamps sesuai schema lama
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();

            // index sesuai schema lama
            $table->index('paket_id');
            $table->index('dibuat_oleh', 'idx_dibuat_oleh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_expenses');
    }
};
