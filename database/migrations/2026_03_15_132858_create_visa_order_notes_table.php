<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_order_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_order_id')->constrained('visa_orders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('note_type', [
                'internal',
                'customer',
                'system',
            ])->default('internal');

            $table->text('note');

            $table->timestamps();

            $table->index('note_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_order_notes');
    }
};