<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_invoices', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->string('invoice_number', 50)->unique('uniq_invoice_number');

            // relasi (FK nanti)
            $table->unsignedBigInteger('pnr_id');

            $table->bigInteger('total_amount')->default(0);
            $table->bigInteger('paid_amount')->default(0);
            $table->bigInteger('refunded_amount')->default(0);

            $table->string('status', 30)->default('UNPAID');

            $table->unsignedInteger('created_by')->nullable();

            // schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('pnr_id', 'idx_pnr');
            $table->index('status', 'idx_ticket_invoices_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_invoices');
    }
};
