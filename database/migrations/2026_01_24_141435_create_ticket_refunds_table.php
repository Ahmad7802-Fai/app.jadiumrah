<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_refunds', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('ticket_invoice_id');

            $table->unsignedBigInteger('amount');

            $table->string('reason', 255)->nullable();

            $table->timestamp('refunded_at')->useCurrent();

            $table->unsignedBigInteger('refunded_by')->nullable();

            $table->string('status', 30); // REFUNDED | PARTIAL
            $table->string('approval_status', 30)->default('PENDING');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            // index sesuai schema lama
            $table->index('ticket_invoice_id', 'idx_refund_invoice');
            $table->index('refunded_at', 'idx_refund_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_refunds');
    }
};
