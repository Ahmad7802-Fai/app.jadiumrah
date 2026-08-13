<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_invoices_backup_20260106', function (Blueprint $table) {
            // ⚠️ id BUKAN auto increment (sesuai backup)
            $table->unsignedBigInteger('id')->default(0);

            $table->string('invoice_number', 50);

            $table->unsignedBigInteger('pnr_id');

            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();

            $table->string('customer_type', 20)->default('AGENT');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150)->nullable();

            $table->bigInteger('total_amount')->default(0);
            $table->bigInteger('paid_amount')->default(0);
            $table->bigInteger('outstanding_amount')->default(0);

            $table->string('status', 30)->default('UNPAID');

            $table->text('notes')->nullable();

            $table->unsignedInteger('created_by')->nullable();

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // ❌ TIDAK ADA PRIMARY KEY di schema lama → dipertahankan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_invoices_backup_20260106');
    }
};
