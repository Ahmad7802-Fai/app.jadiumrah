<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('layanan_invoices', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED

    $table->string('no_invoice', 100);

    // 🔥 FIX UTAMA (FK)
    $table->unsignedBigInteger('id_transaksi');

    $table->decimal('amount', 15, 2)->default(0);
    $table->string('currency', 10)->default('IDR');
    $table->decimal('paid_amount', 15, 2)->default(0);

    $table->enum('status', ['unpaid', 'partial', 'paid', 'cancelled'])
          ->default('unpaid');

    $table->date('due_date')->nullable();

    $table->dateTime('created_at')->useCurrent();
    $table->dateTime('updated_at')->useCurrentOnUpdate();

    $table->index('id_transaksi', 'layanan_id');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_invoices');
    }
};
