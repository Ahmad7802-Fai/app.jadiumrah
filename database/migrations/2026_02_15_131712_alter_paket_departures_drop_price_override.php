<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_departures', function (Blueprint $table) {
            if (Schema::hasColumn('paket_departures', 'price_override')) {
                $table->dropColumn('price_override');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paket_departures', function (Blueprint $table) {
            $table->decimal('price_override', 15, 2)->nullable()->after('booked');
        });
    }
};
