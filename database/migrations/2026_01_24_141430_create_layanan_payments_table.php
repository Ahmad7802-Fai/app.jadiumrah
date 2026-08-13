<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan_payments', function (Blueprint $table) {
    $table->id(); // BIGINT UNSIGNED

    // 🔥 FIX UTAMA (FK)
    $table->unsignedBigInteger('layanan_invoice_id');

    $table->decimal('amount', 15, 2);
    $table->string('currency', 10)->default('IDR');

    $table->string('bank', 100)->nullable();
    $table->string('reference_no', 100)->nullable();
    $table->string('payer_name')->nullable();
    $table->string('proof_filename')->nullable();

    $table->enum('status', ['pending', 'approved', 'rejected'])
          ->default('pending');

    $table->unsignedBigInteger('validated_by')->nullable();
    $table->dateTime('validated_at')->nullable();

    $table->text('validation_note')->nullable();
    $table->string('payment_method', 50)->nullable();
    $table->text('catatan')->nullable();

    $table->dateTime('created_at')->useCurrent();
    $table->dateTime('updated_at')->nullable();

    $table->index('layanan_invoice_id');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_payments');
    }
};
