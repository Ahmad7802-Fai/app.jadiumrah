<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->string('family_id', 50)
                  ->nullable()
                  ->after('agent_id')
                  ->index();
        });
    }

    public function down(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropColumn('family_id');
        });
    }
};
