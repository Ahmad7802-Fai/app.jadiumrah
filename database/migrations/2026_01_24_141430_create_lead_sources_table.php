<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->string('nama_sumber');
            $table->enum('tipe', ['offline', 'online']);

            $table->string('platform', 50)->nullable();
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_sources');
    }
};
