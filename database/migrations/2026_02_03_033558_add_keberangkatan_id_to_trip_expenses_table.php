<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {

            // kolom baru (nullable biar aman)
            $table->unsignedBigInteger('keberangkatan_id')
                  ->nullable()
                  ->after('paket_id');

            // index biar query cepat
            $table->index('keberangkatan_id');

            // FK (optional tapi RECOMMENDED)
            $table->foreign('keberangkatan_id')
                  ->references('id')
                  ->on('keberangkatan')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {

            $table->dropForeign(['keberangkatan_id']);
            $table->dropIndex(['keberangkatan_id']);
            $table->dropColumn('keberangkatan_id');
        });
    }
};
