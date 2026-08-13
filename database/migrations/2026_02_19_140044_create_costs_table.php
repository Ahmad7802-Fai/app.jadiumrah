<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('paket_departure_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('cost_category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | COST DATA
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 15, 2);

            $table->enum('type', [
                'fixed',      // hotel, bus, visa total
                'variable'    // per seat
            ])->default('fixed');

            $table->text('note')->nullable();

            /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX FOR PERFORMANCE
            |--------------------------------------------------------------------------
            */

            $table->index(['paket_departure_id','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};