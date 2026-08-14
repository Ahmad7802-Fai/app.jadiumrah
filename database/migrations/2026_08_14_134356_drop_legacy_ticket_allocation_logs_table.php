<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('ticket_allocation_logs');
    }

    public function down(): void
    {
        Schema::create('ticket_allocation_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('allocation_id');

            $table->bigInteger('received_amount');
            $table->date('received_date')->nullable();

            $table->timestamp('created_at')
                ->useCurrent()
                ->nullable();

            $table->index('allocation_id', 'idx_allocation');
        });
    }
};
