<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // RELATION CORE
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jamaah_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('departure_id')->constrained('paket_departures')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();

            // PAYMENT IDENTITY
            $table->string('payment_code')->unique();

            // TYPE
            $table->enum('type', [
                'dp',
                'pelunasan',
                'add_on',
                'upgrade',
                'lainnya'
            ]);

            // METHOD
            $table->enum('method', [
                'transfer',
                'cash',
                'gateway',
                'edc'
            ]);

            // AMOUNT
            $table->decimal('amount', 15, 2);

            // STATUS
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'cancelled'
            ])->default('paid');

            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();

            // AUDIT
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            // INDEX FOR PERFORMANCE
            $table->index(['departure_id']);
            $table->index(['branch_id']);
            $table->index(['status']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};