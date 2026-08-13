<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_invoice_items', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('ticket_invoice_id');

            $table->string('description')->nullable();

            $table->integer('qty')->default(1);

            $table->bigInteger('unit_price')->default(0);
            $table->bigInteger('subtotal')->default(0);

            // schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();

            // index sesuai schema lama
            $table->index('ticket_invoice_id', 'idx_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_invoice_items');
    }
};
