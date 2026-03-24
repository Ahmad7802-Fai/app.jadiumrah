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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();

        $table->foreignId('departure_id')
              ->constrained('paket_departures')
              ->cascadeOnDelete();

        $table->string('hotel_name')->nullable(); // Makkah / Madinah
        $table->string('city')->nullable();       // makkah / madinah
        $table->string('room_number');            // 101 / A1 / etc

        $table->enum('gender', ['male','female'])->nullable();
        $table->integer('capacity')->default(4);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
