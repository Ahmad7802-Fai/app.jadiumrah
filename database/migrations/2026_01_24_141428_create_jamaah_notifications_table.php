<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_notifications', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');


            $table->string('title', 150)->nullable();
            $table->text('message')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // schema lama: created_at timestamp nullable
            $table->timestamp('created_at')->nullable();

            // index sesuai schema lama
            $table->index('jamaah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_notifications');
    }
};
