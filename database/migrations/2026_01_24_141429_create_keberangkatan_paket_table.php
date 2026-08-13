<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('keberangkatan_paket', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('keberangkatan_id');
            $table->unsignedInteger('paket_master_id');

            $table->bigInteger('harga_quad');
            $table->bigInteger('harga_triple');
            $table->bigInteger('harga_double');

            $table->boolean('is_active')->default(true);

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // unique sesuai schema lama
            $table->unique(
                ['keberangkatan_id', 'paket_master_id'],
                'unik_keberangkatan_paket'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_paket');
    }
};
