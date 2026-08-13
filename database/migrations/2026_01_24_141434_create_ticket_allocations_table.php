<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_allocations', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('pnr_id');

            $table->string('allocation_code', 100)->nullable();

            $table->bigInteger('allocated_amount');

            $table->date('allocation_date')->nullable();

            $table->string('status', 30)->default('ALLOCATED');

            // schema lama: timestamp
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('pnr_id', 'idx_pnr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_allocations');
    }
};
