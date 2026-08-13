<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manifest', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('keberangkatan_id');
            $table->unsignedInteger('jamaah_id');

            $table->enum('tipe_kamar', ['Quad', 'Triple', 'Double']);
            $table->string('nomor_kamar', 50)->nullable();

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('keberangkatan_id');
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manifest');
    }
};
