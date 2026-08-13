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
        Schema::create('flight_manifests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('flight_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('departure_id')
                ->constrained('paket_departures')
                ->cascadeOnDelete();

            $table->timestamp('generated_at');
            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('file_path')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_manifests');
    }
};
