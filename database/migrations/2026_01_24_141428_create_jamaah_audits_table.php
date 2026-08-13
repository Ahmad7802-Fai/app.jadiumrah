<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_audits', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('jamaah_id');

            $table->string('action', 20);

            // json audit
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            $table->unsignedBigInteger('performed_by')->nullable();

            // schema lama: timestamp + default current & on update
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            // index sesuai schema lama
            $table->index('jamaah_id', 'idx_jamaah_audits_jamaah');
            $table->index('action', 'idx_jamaah_audits_action');
            $table->index('performed_by', 'idx_jamaah_audits_performed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_audits');
    }
};
