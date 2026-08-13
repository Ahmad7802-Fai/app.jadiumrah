<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_departure_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_departure_id')->constrained()->cascadeOnDelete();
            $table->enum('room_type', ['double','triple','quad']);
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->unique(['paket_departure_id','room_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_departure_prices');
    }
};