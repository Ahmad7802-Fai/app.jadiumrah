<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // DATA UTAMA
            // ===============================
            $table->string('nama');
            $table->string('no_hp', 50);
            $table->string('email')->nullable();

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('sumber_id');
            $table->enum('channel', ['offline', 'online']);

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('pipeline_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            // ===============================
            // CATATAN & STATUS
            // ===============================
            $table->text('catatan')->nullable();

            $table->enum('status', [
                'NEW',
                'ASSIGNED',
                'ACTIVE',
                'CLOSING',
                'CLOSED',
                'DROPPED',
            ])->default('NEW');

            $table->dateTime('closed_at')->nullable();
            $table->dateTime('dropped_at')->nullable();
            $table->text('drop_reason')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('branch_id', 'idx_leads_branch');
            $table->index('pipeline_id', 'idx_leads_pipeline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
