<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_srp', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('jamaah_id');

            $table->string('nomor_surat', 100);

            // schema lama
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_srp');
    }
};
