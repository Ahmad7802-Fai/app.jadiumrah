<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {

            $table->id();

            $table->foreignId('paket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('discount_type', ['fixed', 'percent']);
            $table->decimal('value', 15, 2);

            $table->timestamp('start_at');
            $table->timestamp('end_at');

            $table->integer('seat_limit')->nullable();
            $table->integer('used_seat')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sales');
    }
};