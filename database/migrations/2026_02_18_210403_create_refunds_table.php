<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
        $table->id();

        $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
        $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
        $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

        $table->string('refund_code')->unique();

        $table->decimal('amount', 15, 2);
        $table->text('reason')->nullable();

        $table->enum('status', ['pending','approved','rejected'])->default('pending');

        $table->foreignId('created_by')->constrained('users');
        $table->foreignId('approved_by')->nullable()->constrained('users');

        $table->timestamp('approved_at')->nullable();

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
