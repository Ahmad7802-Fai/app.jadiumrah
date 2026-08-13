<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('item_id');

            $table->integer('stok')->default(0);

            // schema lama: hanya updated_at
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};
