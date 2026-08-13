<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('title', 150);
            $table->string('photo')->nullable();
            $table->string('category', 100)->nullable();

            // schema lama: datetime nullable
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery');
    }
};
