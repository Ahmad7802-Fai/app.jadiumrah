<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan_master', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->string('kode_layanan', 50);
            $table->string('nama_layanan');
            $table->enum('kategori', ['ticket', 'visa', 'land', 'other'])
                  ->default('other');

            $table->text('deskripsi')->nullable();
            $table->boolean('status')->default(true);

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_master');
    }
};
