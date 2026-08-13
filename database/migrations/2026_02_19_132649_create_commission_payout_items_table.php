<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_payout_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commission_payout_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('commission_log_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->timestamps();

            $table->unique([
                'commission_payout_id',
                'commission_log_id'
            ], 'payout_log_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_payout_items');
    }
};