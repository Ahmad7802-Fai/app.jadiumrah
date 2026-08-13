<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_umrah', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('title');
            $table->string('slug');
            $table->string('seo_title');

            $table->date('tglberangkat');

            $table->string('pesawat');
            $table->string('flight', 100);

            $table->integer('durasi');
            $table->integer('seat');

            $table->string('hotmekkah', 100);
            $table->integer('rathotmekkah');

            $table->string('hotmadinah', 100);
            $table->integer('rathotmadinah');

            $table->integer('quad');
            $table->integer('triple');
            $table->integer('double');

            $table->text('itin');
            $table->string('photo');

            $table->enum('thaif', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('dubai', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('kereta', ['Ya', 'Tidak'])->default('Tidak');

            $table->text('deskripsi');

            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');

            // schema lama pakai char(1)
            $table->char('is_active', 1)->default('1');

            // schema lama: datetime nullable
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->boolean('allow_self_register')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_umrah');
    }
};
