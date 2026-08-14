<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_allocations', function (Blueprint $table) {
            $table->dropColumn('allocation_code');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_allocations', function (Blueprint $table) {
            $table->string('allocation_code', 100)
                ->nullable()
                ->after('pnr_id');
        });
    }
};
