<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pipeline_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('from_pipeline_id')->nullable();
            $table->unsignedInteger('to_pipeline_id');

            $table->string('from_pipeline_name', 50)->nullable();
            $table->string('to_pipeline_name', 50);

            $table->string('action', 50); // DRAG | MANUAL | SUBMIT_CLOSING | APPROVE | REJECT

            $table->unsignedBigInteger('created_by')->nullable();

            // schema lama
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('changed_at')->useCurrent();

            // index sesuai schema lama
            $table->index('lead_id');
            $table->index('to_pipeline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_logs');
    }
};
