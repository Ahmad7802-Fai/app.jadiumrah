<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
    |--------------------------------------------------------------------------
    | UP
    |--------------------------------------------------------------------------
    */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->decimal('discount_snapshot', 15, 2)
                ->nullable()
                ->after('original_price_snapshot');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | DOWN
    |--------------------------------------------------------------------------
    */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->dropColumn('discount_snapshot');

        });
    }
};