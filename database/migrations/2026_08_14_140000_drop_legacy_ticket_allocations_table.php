<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('ticket_allocations');
    }

    public function down(): void
    {
        Schema::create('ticket_allocations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            $table->unsignedBigInteger('pnr_id');

            $table->bigInteger('allocated_amount');
            $table->date('allocation_date')->nullable();

            $table->string('status', 30)
                ->default('ALLOCATED');

            $table->timestamp('created_at')
                ->nullable()
                ->useCurrent();

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate();

            $table->index('pnr_id', 'idx_pnr');

            $table->foreign('pnr_id')
                ->references('id')
                ->on('ticket_pnrs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
