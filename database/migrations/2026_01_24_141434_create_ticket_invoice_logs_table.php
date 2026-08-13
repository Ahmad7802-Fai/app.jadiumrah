<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_invoice_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('ticket_invoice_id');

            $table->string('action', 100)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('created_by')->nullable();

            // schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();

            // index sesuai schema lama
            $table->index('ticket_invoice_id', 'idx_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_invoice_logs');
    }
};
