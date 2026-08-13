<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            // tidak ada id (pivot table)
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');

            // index sesuai schema lama
            $table->index('user_id', 'user_roles_user_id_foreign');
            $table->index('role_id', 'user_roles_role_id_foreign');

            // ❌ FK sengaja ditunda
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
