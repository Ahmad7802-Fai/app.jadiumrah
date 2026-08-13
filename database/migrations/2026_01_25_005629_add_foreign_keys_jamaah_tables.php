<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ===============================
        // JAMAAH (parent)
        // ===============================
        Schema::table('jamaah', function (Blueprint $table) {
            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('agent_id')
                ->references('id')->on('agents')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        // ===============================
        // JAMAAH USERS
        // ===============================
        Schema::table('jamaah_users', function (Blueprint $table) {
            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // PASSPORT JAMAAH
        // ===============================
        Schema::table('passport_jamaah', function (Blueprint $table) {
            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // VISA
        // ===============================
        Schema::table('visa', function (Blueprint $table) {
            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // ===============================
        // JAMAAH NOTIFICATIONS
        // ===============================
        Schema::table('jamaah_notifications', function (Blueprint $table) {
            $table->foreign('jamaah_id')
                ->references('id')->on('jamaah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jamaah_notifications', fn (Blueprint $t) => $t->dropForeign(['jamaah_id']));
        Schema::table('visa', fn (Blueprint $t) => $t->dropForeign(['jamaah_id']));
        Schema::table('passport_jamaah', fn (Blueprint $t) => $t->dropForeign(['jamaah_id']));
        Schema::table('jamaah_users', fn (Blueprint $t) => $t->dropForeign(['jamaah_id']));
        Schema::table('jamaah', function (Blueprint $t) {
            $t->dropForeign(['branch_id']);
            $t->dropForeign(['agent_id']);
        });
    }
};

