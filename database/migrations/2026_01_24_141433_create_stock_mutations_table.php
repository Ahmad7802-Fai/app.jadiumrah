<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('item_id');

            // schema lama
            $table->dateTime('tanggal')->useCurrent();

            $table->enum('tipe', ['IN', 'OUT']);

            $table->integer('jumlah');

            $table->text('keterangan')->nullable();
            $table->string('sumber', 100)->nullable();
            $table->unsignedInteger('referensi_id')->nullable();

            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
