<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('topup_id')->nullable();
            $table->unsignedInteger('jamaah_id')->nullable();

            $table->string('phone', 20);

            $table->enum('type', ['APPROVE', 'REJECT', 'RESEND']);
            $table->enum('status', ['SUCCESS', 'FAILED']);

            $table->text('message')->nullable();
            $table->text('error')->nullable();

            // timestamp sesuai schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();

            // index sesuai schema lama
            $table->index('topup_id', 'idx_wa_logs_topup');
            $table->index('jamaah_id', 'idx_wa_logs_jamaah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
    }
};
