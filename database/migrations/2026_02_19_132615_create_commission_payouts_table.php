<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();

            $table->string('payout_code')->unique();

            $table->foreignId('agent_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->decimal('total_amount', 15, 2)->default(0);

            $table->enum('status', [
                'request',     // diajukan agent
                'approved',    // disetujui finance
                'paid',        // sudah dibayarkan
                'rejected'     // ditolak
            ])->default('request');

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('paid_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('paid_at')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_payouts');
    }
};