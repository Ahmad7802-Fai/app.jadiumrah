<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_invoice_items', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('id_invoice');
            $table->unsignedInteger('id_layanan_transaksi');

            $table->integer('qty')->default(1);

            $table->decimal('harga', 20, 2)->default(0);
            $table->decimal('subtotal', 20, 2)->default(0);

            // index sesuai schema lama
            $table->index('id_invoice');
            $table->index('id_layanan_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_invoice_items');
    }
};
