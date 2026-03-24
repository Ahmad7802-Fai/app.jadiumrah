<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_destinations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paket_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('destination_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->unsignedInteger('day_order');  // Hari ke berapa

            $table->text('note')->nullable();      // Optional keterangan

            $table->timestamps();

            $table->unique(['paket_id', 'day_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_destinations');
    }
};