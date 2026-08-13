<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_master', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('nama_paket');
            $table->string('pesawat', 150)->nullable();
            $table->string('hotel_mekkah')->nullable();
            $table->string('hotel_madinah')->nullable();

            $table->bigInteger('harga_quad')->nullable();
            $table->bigInteger('harga_triple')->nullable();
            $table->bigInteger('harga_double')->nullable();

            $table->bigInteger('diskon_default')->default(0);

            // schema lama pakai char(1)
            $table->char('is_active', 1)->default('1');

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_master');
    }
};
