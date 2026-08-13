<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_installments', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('invoice_id');

            $table->date('tanggal');
            $table->bigInteger('jumlah');

            $table->enum('metode', ['cash', 'transfer']);
            $table->string('bukti_transfer')->nullable();

            $table->enum('status', ['pending', 'valid'])->default('pending');

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_installments');
    }
};
