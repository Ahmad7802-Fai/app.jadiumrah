<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            // ===============================
            // PRIMARY KEY
            // ===============================
            $table->id(); // bigint unsigned auto increment

            // ===============================
            // RELASI (KOLOM SAJA, FK DI PHASE)
            // ===============================
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('user_id')->nullable();

            // ===============================
            // AKTIVITAS
            // ===============================
            $table->enum('aktivitas', [
                'wa',
                'telpon',
                'dm',
                'meeting',
                'kunjungan',
                'presentasi',
                'followup',
                'closing',
            ]);

            $table->text('hasil')->nullable();
            $table->text('next_action')->nullable();
            $table->dateTime('followup_date')->nullable();

            // ===============================
            // TIMESTAMP (SCHEMA LAMA)
            // ===============================
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            // ===============================
            // INDEX (SESUAI SCHEMA LAMA)
            // ===============================
            $table->index('user_id', 'idx_lead_activities_user');
            $table->index('lead_id', 'idx_lead_activities_lead');
            $table->index(
                ['lead_id', 'created_at'],
                'idx_lead_activities_lead_created'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
