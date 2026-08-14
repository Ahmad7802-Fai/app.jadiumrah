<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('ticket_deposits');
    }

    public function down(): void
    {
        Schema::create('ticket_deposits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pnr_id');
            $table->unsignedInteger('agent_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();

            $table->bigInteger('amount');

            $table->string('status', 30)->default('PENDING');

            $table->string('bank_recipient', 100)->nullable();
            $table->string('sender', 100)->nullable();

            $table->date('transfer_date')->nullable();

            $table->string('receipt_file')->nullable();

            $table->string('source', 50)->default('TOPUP');

            $table->timestamp('created_at')
                ->nullable()
                ->useCurrent();

            $table->timestamp('updated_at')
                ->nullable()
                ->useCurrentOnUpdate();

            $table->index('pnr_id', 'idx_pnr');
            $table->index('agent_id', 'idx_agent');
            $table->index('branch_id', 'idx_branch');
        });
    }
};
