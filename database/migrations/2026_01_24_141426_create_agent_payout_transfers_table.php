<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_payout_transfers', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti, sekarang kolom dulu)
            $table->unsignedBigInteger('payout_id')->unique();

            // bank info
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 100);

            // transfer info
            $table->bigInteger('amount');
            $table->dateTime('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();

            // bukti & catatan
            $table->string('transfer_proof')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_payout_transfers');
    }
};

