<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_list', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('keberangkatan_id');
            $table->unsignedInteger('jamaah_id')->nullable();

            $table->string('nomor_kamar', 50);
            $table->enum('tipe_kamar', ['Quad', 'Triple', 'Double']);

            // index sesuai schema lama
            $table->index('keberangkatan_id');
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_list');
    }
};
