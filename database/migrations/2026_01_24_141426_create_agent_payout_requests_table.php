<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_payout_requests', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->unsignedInteger('agent_id');
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->unsignedBigInteger('total_komisi')->default(0);
            $table->unsignedInteger('total_item')->default(0);

            $table->enum('status', [
                'requested',
                'approved',
                'rejected',
                'paid',
            ])->default('requested');

            $table->dateTime('requested_at');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();

            $table->unsignedInteger('requested_by')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->unsignedInteger('paid_by')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();

            // index
            $table->index(['agent_id', 'status'], 'idx_agent_status');
            $table->index('status', 'idx_status');
        });

        /*
         | CHECK constraint dari schema lama:
         | status = 'paid' WAJIB punya paid_at & paid_by
         |
         | MySQL CHECK sering diabaikan versi lama,
         | nanti lebih aman dipindah ke:
         | - validation
         | - model observer
         | - service layer
         */
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payout_requests');
    }
};
