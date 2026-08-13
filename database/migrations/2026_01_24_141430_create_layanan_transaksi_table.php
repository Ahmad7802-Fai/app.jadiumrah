<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan_transaksi', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED

    // 🔥 FIX UTAMA
    $table->unsignedBigInteger('id_client');

    $table->string('currency', 10)->default('IDR');
    $table->decimal('subtotal', 20, 2)->default(0);

    $table->text('notes')->nullable();

    $table->enum('status', [
        'pending',
        'invoiced',
        'paid',
        'canceled',
    ])->default('pending');

    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('updated_by')->nullable();

    $table->dateTime('created_at')->useCurrent();
    $table->dateTime('updated_at')->useCurrentOnUpdate();

    $table->index('id_client');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_transaksi');
    }
};
