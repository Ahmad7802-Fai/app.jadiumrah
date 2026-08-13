<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_payments', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('layanan_item_id')->nullable(); // 🔥 FIX

        $table->string('vendor_name');
        $table->string('invoice_number', 100)->nullable();
        $table->decimal('amount', 15, 2);
        $table->string('currency', 10)->default('IDR');

        $table->string('payment_method', 50)->nullable();
        $table->string('bank', 100)->nullable();
        $table->string('reference_no', 100)->nullable();
        $table->date('payment_date');

        $table->string('proof_file')->nullable();
        $table->text('notes')->nullable();

        $table->dateTime('created_at')->useCurrent();
        $table->dateTime('updated_at')->useCurrentOnUpdate();

        $table->index('layanan_item_id');
    });


    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
