<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('jamaah_user_id')->nullable();

            $table->string('action', 50);
            $table->string('description')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // schema lama
            $table->timestamp('created_at')->useCurrent()->nullable();

            // index sesuai schema lama
            $table->index('jamaah_user_id', 'idx_jamaah_user');
            $table->index('action', 'idx_action');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
