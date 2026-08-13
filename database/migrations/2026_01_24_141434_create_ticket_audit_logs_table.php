<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_audit_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->string('entity_type', 50); // INVOICE | PAYMENT | PNR
            $table->unsignedBigInteger('entity_id');

            $table->string('action', 100);

            $table->json('before')->nullable();
            $table->json('after')->nullable();

            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('actor_role', 50)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // schema lama
            $table->timestamp('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index(['entity_type', 'entity_id'], 'idx_audit_entity');
            $table->index('actor_id', 'idx_audit_actor');
            $table->index('created_at', 'idx_audit_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_audit_logs');
    }
};
