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
        Schema::create('flight_segments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flight_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('segment_order');

            $table->string('origin');
            $table->string('destination');

            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');

            $table->string('terminal')->nullable();
            $table->string('gate')->nullable();

            $table->timestamps();

            $table->unique(['flight_id','segment_order']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_segments');
    }
};
