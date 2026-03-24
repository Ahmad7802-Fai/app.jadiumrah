<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('paket_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained()->cascadeOnDelete();
            $table->enum('city', ['mekkah', 'madinah']);
            $table->string('hotel_name');
            $table->integer('rating')->nullable();
            $table->string('distance_to_haram')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_hotels');
    }
};
