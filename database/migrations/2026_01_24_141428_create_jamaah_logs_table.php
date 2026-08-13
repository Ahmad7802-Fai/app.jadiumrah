<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jamaah_logs', function (Blueprint $table) {
            $table->id(); // int auto increment

            // relasi (FK nanti)
$table->unsignedBigInteger('jamaah_id');


            $table->enum('action', [
                'create',
                'update',
                'approve',
                'reject',
                'delete',
            ]);

            $table->json('meta')->nullable();

            $table->unsignedInteger('created_by');

            // schema lama: created_at saja
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamaah_logs');
    }
};
