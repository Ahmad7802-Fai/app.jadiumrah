<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('team', function (Blueprint $table) {
            $table->id(); // int unsigned auto increment

            $table->string('nama', 150);
            $table->string('jabatan', 150);

            $table->string('photo')->nullable();
            $table->text('deskripsi')->nullable();

            // schema lama: datetime nullable
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team');
    }
};
