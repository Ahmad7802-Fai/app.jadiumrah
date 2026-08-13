<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_order_id')->constrained('visa_orders')->cascadeOnDelete();

            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('description')->nullable();

            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->nullable();

            $table->timestamps();

            $table->index('to_status');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_status_histories');
    }
};