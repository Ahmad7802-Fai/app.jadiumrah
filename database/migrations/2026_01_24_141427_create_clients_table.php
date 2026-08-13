<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // int auto increment

            $table->enum('tipe', ['b2b', 'b2c'])->default('b2c');
            $table->string('nama');

            $table->string('pic')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('npwp', 50)->nullable();

            // schema lama pakai datetime + default current
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
