<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('kategori', 100);
            $table->text('deskripsi')->nullable();

            $table->bigInteger('jumlah');
            $table->date('tanggal');

            $table->string('bukti')->nullable();

            // relasi (FK nanti)
            $table->unsignedInteger('dibuat_oleh');

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('dibuat_oleh');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_expenses');
    }
};
