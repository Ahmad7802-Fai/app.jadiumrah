<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('komisi_logs', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedInteger('jamaah_id');
            $table->unsignedInteger('payment_id')->nullable();
            $table->unsignedInteger('agent_id');
            $table->unsignedBigInteger('payout_request_id')->nullable();
            $table->unsignedInteger('branch_id');

            $table->enum('mode', ['manual', 'affiliate']);

            $table->decimal('komisi_persen', 5, 2);
            $table->bigInteger('komisi_nominal');

            $table->enum('status', [
                'pending',
                'available',
                'requested',
                'paid',
                'rejected',
            ]);

            $table->dateTime('requested_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('reject_reason')->nullable();

            // schema lama: datetime + default current & on update
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('payment_id', 'fk_komisi_logs_payment');
            $table->index('payout_request_id', 'idx_payout_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisi_logs');
    }
};
