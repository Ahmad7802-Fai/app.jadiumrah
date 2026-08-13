<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_routes', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            // relasi (FK nanti)
            $table->unsignedBigInteger('pnr_id');

            $table->string('flight_number', 20)->nullable();
            $table->integer('sector')->default(1);

            $table->string('origin', 10)->nullable();
            $table->string('destination', 10)->nullable();

            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();

            $table->tinyInteger('arrival_day_offset')
                  ->default(0)
                  ->comment('0 = same day, 1 = +1 day');

            // timestamps sesuai schema lama
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // index sesuai schema lama
            $table->index('pnr_id', 'idx_pnr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_routes');
    }
};
