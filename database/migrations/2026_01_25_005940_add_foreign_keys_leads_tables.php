<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===============================
        // LEADS (parent)
        // ===============================
        Schema::table('leads', function (Blueprint $table) {
            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // ===============================
        // LEAD ACTIVITIES
        // ===============================
        Schema::table('lead_activities', function (Blueprint $table) {
            $table->foreign('lead_id')
                ->references('id')->on('leads')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // atau nullOnDelete jika nullable
        });

        // ===============================
        // LEAD CLOSINGS
        // ===============================
        Schema::table('lead_closings', function (Blueprint $table) {
            $table->foreign('lead_id')
                ->references('id')->on('leads')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lead_closings', fn (Blueprint $t) => $t->dropForeign(['lead_id']));
        Schema::table('lead_activities', function (Blueprint $t) {
            $t->dropForeign(['lead_id']);
            $t->dropForeign(['user_id']);
        });
        Schema::table('leads', fn (Blueprint $t) => $t->dropForeign(['branch_id']));
    }
};

