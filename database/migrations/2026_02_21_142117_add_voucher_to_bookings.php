<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (!Schema::hasColumn('bookings', 'voucher_id')) {
                $table->foreignId('voucher_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            }

        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            if (Schema::hasColumn('bookings', 'voucher_id')) {
                $table->dropForeign(['voucher_id']);
                $table->dropColumn('voucher_id');
            }

        });
    }
};