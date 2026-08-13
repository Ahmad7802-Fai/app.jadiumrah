<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_closings', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('jamaah_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            // ===============================
            // NILAI & STATUS
            // ===============================
            $table->decimal('nominal_dp', 15, 2)->nullable();
            $table->decimal('total_paket', 15, 2)->nullable();

            $table->enum('status', ['DRAFT', 'APPROVED', 'REJECTED'])
                  ->default('DRAFT');

            $table->dateTime('closed_at')->nullable();
            $table->text('catatan')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // ===============================
            // CONSTRAINT & INDEX
            // ===============================
            // sesuai schema lama: 1 lead = 1 closing
            $table->unique('lead_id', 'uniq_lead_closing_jamaah');

            // index tambahan (opsional tapi aman)
            $table->index('jamaah_id');
            $table->index('agent_id');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_closings');
    }
};
