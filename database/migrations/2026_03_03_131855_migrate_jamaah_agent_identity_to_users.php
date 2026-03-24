<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. DROP FOREIGN KEY KE AGENTS
        |--------------------------------------------------------------------------
        */
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | 2. MIGRATE DATA (agents.id → users.id)
        |--------------------------------------------------------------------------
        */
        DB::statement("
            UPDATE jamaahs j
            JOIN agents a ON j.agent_id = a.id
            SET j.agent_id = a.user_id
        ");

        /*
        |--------------------------------------------------------------------------
        | 3. ADD FOREIGN KEY BARU KE USERS
        |--------------------------------------------------------------------------
        */
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->foreign('agent_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rollback (Kembalikan ke agents)
        |--------------------------------------------------------------------------
        */

        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
        });

        DB::statement("
            UPDATE jamaahs j
            JOIN agents a ON j.agent_id = a.user_id
            SET j.agent_id = a.id
        ");

        Schema::table('jamaahs', function (Blueprint $table) {
            $table->foreign('agent_id')
                ->references('id')
                ->on('agents')
                ->nullOnDelete();
        });
    }
};