<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_costs', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');


            $table->string('deskripsi');
            $table->bigInteger('jumlah');
            $table->date('tanggal');

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_costs');
    }
};
