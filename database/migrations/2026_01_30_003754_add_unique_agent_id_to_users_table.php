<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Pastikan kolom ada
            if (Schema::hasColumn('users', 'agent_id')) {

                // UNIQUE untuk memastikan 1 user = 1 agent
                $table->unique('agent_id', 'users_agent_id_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_agent_id_unique');
        });
    }
};
