<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('keberangkatan', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('id_paket_master')->nullable();


            $table->string('kode_keberangkatan', 50)->nullable();

            $table->date('tanggal_berangkat')->nullable();
            $table->date('tanggal_pulang')->nullable();

            $table->integer('kuota')->default(0);
            $table->integer('seat_terisi')->default(0);
            $table->integer('jumlah_jamaah')->default(0);

            $table->enum('status', ['Aktif', 'Selesai', 'Batal'])->default('Aktif');

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('id_paket_master', 'fk_keberangkatan_paket_master');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keberangkatan');
    }
};
