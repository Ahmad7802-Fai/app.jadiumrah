<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_pnrs', function (Blueprint $table) {
    $table->id(); // bigint unsigned

    $table->string('pnr_code', 20)->unique('uniq_pnr_code');

    // 🔥 FIX: SEMUA FK → unsignedBigInteger
    $table->unsignedBigInteger('client_id')->nullable();
    $table->string('lobc_code', 50)->nullable();
    $table->unsignedBigInteger('agent_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();

    $table->string('category', 50)->nullable();
    $table->string('airline_class', 50)->nullable();
    $table->string('airline_code', 10)->nullable();
    $table->string('airline_name', 100)->nullable();

    $table->integer('pax')->default(0);
    $table->bigInteger('fare_per_pax')->default(0);
    $table->bigInteger('deposit_per_pax')->default(0);
    $table->bigInteger('total_fare')->default(0);
    $table->bigInteger('total_deposit')->default(0);
    $table->bigInteger('balance')->default(0);
    $table->integer('seat')->default(0);

    $table->string('status', 30)->default('ON_FLOW');
    $table->string('po_status', 50)->nullable();

    $table->unsignedBigInteger('created_by')->nullable();

    $table->timestamp('created_at')->nullable()->useCurrent();
    $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

    $table->index('agent_id', 'idx_agent');
    $table->index('branch_id', 'idx_branch');
    $table->index('client_id', 'fk_ticket_pnrs_client');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_pnrs');
    }
};
