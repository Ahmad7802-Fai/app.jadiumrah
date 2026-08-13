<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            // schema lama: int unsigned auto increment
            $table->increments('id');

            // relasi (FK nanti)
            $table->unsignedInteger('user_id');

            $table->text('action');

            // timestamps sesuai schema lama
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
