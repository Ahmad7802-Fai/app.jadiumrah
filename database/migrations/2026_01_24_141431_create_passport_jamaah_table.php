<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passport_jamaah', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');


            $table->string('nomor_paspor', 50)->nullable();
            $table->date('tanggal_terbit_paspor')->nullable();
            $table->date('tanggal_habis_paspor')->nullable();

            $table->string('tempat_terbit_paspor', 100)->nullable();
            $table->string('negara_penerbit', 100)->nullable();

            $table->text('alamat_lengkap')->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('kode_pos', 20)->nullable();

            $table->string('tujuan_imigrasi')->nullable();

            $table->enum(
                'rekomendasi_paspor',
                ['Masih Berlaku', 'Segera Perpanjang', 'Perlu Perpanjang']
            )->nullable();

            // schema lama
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passport_jamaah');
    }
};
