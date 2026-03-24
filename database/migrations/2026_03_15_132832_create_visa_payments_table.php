<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_order_id')->constrained('visa_orders')->cascadeOnDelete();

            $table->string('payment_number')->unique()->nullable();

            $table->enum('payment_method', [
                'bank_transfer',
                'cash',
                'gateway',
                'manual',
            ])->default('bank_transfer');

            $table->decimal('amount', 15, 2)->default(0);

            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'refunded',
            ])->default('pending');

            $table->string('reference_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();

            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_status');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_payments');
    }
};