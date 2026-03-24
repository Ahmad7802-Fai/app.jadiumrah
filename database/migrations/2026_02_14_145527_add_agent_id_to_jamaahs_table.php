<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->foreignId('agent_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('agents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn('agent_id');
        });
    }
};