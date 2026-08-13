<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('jamaah_id');
            $table->unsignedBigInteger('paket_id')->nullable();


            $table->string('nomor_invoice', 50)->unique();
            $table->date('tanggal');

            $table->bigInteger('total_tagihan');
            $table->bigInteger('total_terbayar')->default(0);
            $table->bigInteger('sisa_tagihan')->default(0);

            $table->enum('status', [
                'belum_lunas',
                'menunggu_validasi',
                'cicilan',
                'lunas',
            ])->default('belum_lunas');

            $table->enum('kamar', ['quad', 'triple', 'double', 'single'])->nullable();

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('jamaah_id');
            $table->index('paket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
