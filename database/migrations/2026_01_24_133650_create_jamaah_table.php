<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah', function (Blueprint $table) {
            $table->id();

            // relasi utama
            $table->unsignedInteger('lead_id')->nullable()->unique();
            $table->unsignedInteger('id_keberangkatan')->nullable();
            $table->unsignedInteger('id_paket')->nullable();
            $table->unsignedInteger('id_paket_master')->nullable();

            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();


            // paket & harga
            $table->string('paket');
            $table->string('nama_paket')->nullable();
            $table->bigInteger('harga_default')->nullable();
            $table->bigInteger('harga_disepakati')->nullable();
            $table->bigInteger('diskon')->default(0);
            $table->bigInteger('deposit')->nullable();
            $table->bigInteger('sisa')->nullable();

            // identitas
            $table->string('no_id', 100)->unique();
            $table->string('nik', 50);
            $table->string('nama_lengkap');
            $table->string('nama_passport')->nullable();
            $table->string('nama_ayah');
            $table->string('no_hp', 20)->nullable();

            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->integer('usia')->nullable();
            $table->string('kelompok_usia')->nullable();

            $table->string('status_pernikahan');
            $table->enum('jenis_kelamin', ['L', 'P']);

            // mahram
            $table->string('nama_mahram')->nullable();
            $table->string('status_mahram')->nullable();

            // profil tambahan
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();

            // riwayat & kondisi
            $table->enum('pernah_umroh', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('pernah_haji', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('merokok', ['Ya', 'Tidak'])->default('Tidak');
            $table->enum('penyakit_khusus', ['Ya', 'Tidak'])->default('Tidak');
            $table->string('nama_penyakit')->nullable();
            $table->string('butuh_penanganan_khusus', 20)->default('Tidak');
            $table->enum('kursi_roda', ['Ya', 'Tidak'])->default('Tidak');

            // dokumen & catatan
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();

            // kamar & tipe
            $table->enum('tipe_kamar', ['double', 'triple', 'quad'])->default('quad');
            $table->enum('tipe_jamaah', ['reguler', 'tabungan', 'cicilan'])->default('reguler');

            // workflow
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('rejected_reason')->nullable();

            // source
            $table->enum('source', ['website', 'agent'])->default('website');
            $table->enum('mode', ['manual', 'affiliate'])->default('manual');

            $table->timestamps();

            // index tambahan (sesuai SQL lama)
            $table->index('nama_lengkap');
            $table->index('nik');
            $table->index('no_id');
            $table->index(['agent_id', 'mode']);
            $table->index('tipe_jamaah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah');
    }
};
