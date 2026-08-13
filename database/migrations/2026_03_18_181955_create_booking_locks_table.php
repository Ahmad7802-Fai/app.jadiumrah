<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RUN MIGRATION
     */
    public function up(): void
    {
        Schema::create('booking_locks', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATION
            |--------------------------------------------------------------------------
            */
            $table->foreignId('paket_departure_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | LOCK INFO
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('qty');

            $table->timestamp('expired_at');

            /*
            |--------------------------------------------------------------------------
            | META
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX (🔥 PERFORMANCE)
            |--------------------------------------------------------------------------
            */
            $table->index(['paket_departure_id', 'expired_at']);
            $table->index(['user_id']);
        });
    }

    /**
     * ROLLBACK
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_locks');
    }
};