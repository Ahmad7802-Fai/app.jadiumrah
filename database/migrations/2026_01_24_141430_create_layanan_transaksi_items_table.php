<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan_transaksi_items', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED

    // 🔥 FIX UTAMA (FK)
    $table->unsignedBigInteger('id_transaksi');
    $table->unsignedBigInteger('id_layanan_item');

    $table->integer('qty')->default(1);
    $table->integer('days')->default(1);

    $table->decimal('harga', 20, 2)->default(0);
    $table->decimal('subtotal', 20, 2)->default(0);

    $table->dateTime('created_at')->useCurrent();
    $table->dateTime('updated_at')->useCurrentOnUpdate();

    $table->index('id_transaksi');
    $table->index('id_layanan_item');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_transaksi_items');
    }
};
