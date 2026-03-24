<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('commission_logs', function (Blueprint $table) {

        // Tambah FK jika belum ada
        $table->foreign('jamaah_id')
              ->references('id')
              ->on('jamaahs')
              ->onDelete('cascade');

        // Unique booking + jamaah
        $table->unique(
            ['booking_id','jamaah_id'],
            'commission_logs_booking_jamaah_unique'
        );
    });
}

public function down(): void
{
    Schema::table('commission_logs', function (Blueprint $table) {

        $table->dropUnique('commission_logs_booking_jamaah_unique');
        $table->dropForeign(['jamaah_id']);
    });
}
};
