<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('judul');
            $table->string('slug');
            $table->text('konten')->nullable();

            $table->string('kategori', 100)->nullable();
            $table->string('thumbnail')->nullable();

            // schema lama: datetime nullable
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
