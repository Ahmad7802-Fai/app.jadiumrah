<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_keberangkatan', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');

            $table->unsignedInteger('keberangkatan_id');

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('jamaah_id');
            $table->index('keberangkatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_keberangkatan');
    }
};
