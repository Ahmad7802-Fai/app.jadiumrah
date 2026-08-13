<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visa', function (Blueprint $table) {
            // schema lama: int auto increment
            $table->increments('id');

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');

            $table->unsignedInteger('keberangkatan_id');

            $table->enum('status', ['Proses', 'Approved', 'Rejected'])
                  ->default('Proses');

            $table->string('nomor_visa', 100)->nullable();

            // timestamp sesuai schema lama
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('jamaah_id');
            $table->index('keberangkatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa');
    }
};
