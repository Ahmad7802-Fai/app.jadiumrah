<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_pnrs', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_pnrs', 'deposit_per_pax')) {
                $table->dropColumn('deposit_per_pax');
            }
            if (Schema::hasColumn('ticket_pnrs', 'total_deposit')) {
                $table->dropColumn('total_deposit');
            }
            if (Schema::hasColumn('ticket_pnrs', 'balance')) {
                $table->dropColumn('balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_pnrs', function (Blueprint $table) {
            $table->bigInteger('deposit_per_pax')->default(0);
            $table->bigInteger('total_deposit')->default(0);
            $table->bigInteger('balance')->default(0);
        });
    }
};
