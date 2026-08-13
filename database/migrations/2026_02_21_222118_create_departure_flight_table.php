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
      Schema::create('departure_flight', function (Blueprint $table) {
            $table->id();

            $table->foreignId('departure_id')
                ->constrained('paket_departures')
                ->cascadeOnDelete();

            $table->foreignId('flight_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', ['departure','return']);

            $table->timestamps();

            $table->unique(['departure_id','flight_id','type']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departure_flight');
    }
};
