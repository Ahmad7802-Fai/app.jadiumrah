<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_departures', function (Blueprint $table) {

            $table->string('departure_code', 30)
                  ->nullable()
                  ->after('paket_id');

            $table->string('flight_number', 50)
                  ->nullable()
                  ->after('departure_code');

            $table->string('meeting_point', 255)
                  ->nullable()
                  ->after('flight_number');
        });
    }

    public function down(): void
    {
        Schema::table('paket_departures', function (Blueprint $table) {

            $table->dropColumn([
                'departure_code',
                'flight_number',
                'meeting_point',
            ]);
        });
    }
};