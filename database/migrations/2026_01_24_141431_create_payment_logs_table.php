<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('payment_id')->nullable();

            $table->string('action', 191);
            $table->string('context', 30)->nullable();

            $table->text('meta')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();

            $table->unsignedInteger('created_by')->nullable();

            // schema lama
            $table->dateTime('created_at')->useCurrent();

            // index sesuai schema lama
            $table->index('payment_id', 'idx_payment');
            $table->index(['payment_id', 'action'], 'idx_payment_logs_payment_action');
            $table->index('context', 'idx_payment_logs_context');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
