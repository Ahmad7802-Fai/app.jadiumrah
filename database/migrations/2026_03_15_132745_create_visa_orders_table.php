<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visa_product_id')->constrained('visa_products')->restrictOnDelete();

            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 30);
            $table->text('customer_address')->nullable();

            $table->unsignedInteger('total_travelers')->default(1);

            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();

            $table->string('departure_city')->nullable();
            $table->string('destination_city')->nullable();

            $table->enum('order_status', [
                'draft',
                'pending',
                'waiting_documents',
                'waiting_payment',
                'processing',
                'submitted',
                'approved',
                'rejected',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid',
                'failed',
                'refunded',
            ])->default('unpaid');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);

            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('order_status');
            $table->index('payment_status');
            $table->index('departure_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_orders');
    }
};