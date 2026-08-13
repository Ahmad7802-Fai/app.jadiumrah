<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan_item', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED (default Laravel)

    // 🔥 FIX UTAMA
    $table->unsignedBigInteger('id_layanan_master');

    $table->string('nama_item');
    $table->decimal('harga', 20, 2)->default(0);

    $table->enum('tipe', ['default', 'hotel'])->default('default');
    $table->integer('durasi_hari_default')->nullable();

    $table->string('satuan', 50)->default('unit');
    $table->string('vendor')->nullable();

    $table->date('tanggal_mulai')->nullable();
    $table->date('tanggal_selesai')->nullable();

    $table->string('currency', 10)->default('IDR');
    $table->boolean('status')->default(true);

    // schema lama
    $table->dateTime('created_at')->useCurrent();
    $table->dateTime('updated_at')->useCurrentOnUpdate();

    $table->index('id_layanan_master');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_item');
    }
};
