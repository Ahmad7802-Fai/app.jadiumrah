<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_users', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');



            $table->string('email', 100)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');

            $table->string('remember_token', 100)->nullable();

            $table->timestamp('password_changed_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // schema lama: timestamp nullable
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // index sesuai schema lama
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_users');
    }
};
