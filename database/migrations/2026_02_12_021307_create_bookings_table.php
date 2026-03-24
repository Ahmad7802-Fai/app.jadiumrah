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
Schema::create('bookings', function (Blueprint $table) {

    $table->id();

    $table->foreignId('paket_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('paket_departure_id')
        ->constrained('paket_departures')
        ->cascadeOnDelete();

    $table->foreignId('branch_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->foreignId('agent_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->decimal('total_amount', 15, 2);
    $table->decimal('paid_amount', 15, 2)->default(0);

    $table->enum('status', [
        'draft',
        'confirmed',
        'cancelled'
    ])->default('draft');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
