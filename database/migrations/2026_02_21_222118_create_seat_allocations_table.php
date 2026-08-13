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
        Schema::create('seat_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flight_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('departure_id')
                ->constrained('paket_departures')
                ->cascadeOnDelete();

            $table->integer('total_seat');
            $table->integer('blocked_seat')->default(0);   // internal reserve
            $table->integer('used_seat')->default(0);

            $table->timestamps();

            $table->unique(['flight_id','departure_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_allocations');
    }
};
