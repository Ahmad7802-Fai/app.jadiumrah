<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('distribution', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->dateTime('tanggal')->useCurrent();
            $table->string('tujuan');
            $table->text('catatan')->nullable();

            // schema lama hanya punya created_at
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution');
    }
};
