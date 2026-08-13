<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('nama', 100);
            $table->text('pesan')->nullable();
            $table->string('photo')->nullable();

            $table->integer('rating')->default(5);

            // schema lama: datetime nullable
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};
