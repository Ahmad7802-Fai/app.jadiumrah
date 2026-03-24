<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_departures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paket_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('departure_date');
            $table->date('return_date')->nullable();

            $table->integer('quota');
            $table->integer('booked')->default(0);

            $table->decimal('price_override', 15, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            $table->index(['paket_id','departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_departures');
    }
};