<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * RUN MIGRATION
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INDEX: paket_departures (OPTIMAL UNTUK QUERY API)
        |--------------------------------------------------------------------------
        */
        Schema::table('paket_departures', function (Blueprint $table) {

            // 🔥 CEK DULU BIAR TIDAK DUPLICATE
            $indexes = collect(DB::select("SHOW INDEX FROM paket_departures"))
                ->pluck('Key_name')
                ->toArray();

            if (!in_array('idx_paket_departures_main', $indexes)) {
                $table->index(
                    ['paket_id', 'is_active', 'is_closed', 'departure_date'],
                    'idx_paket_departures_main'
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | INDEX: paket_departure_prices (UNTUK JOIN & SORT PRICE)
        |--------------------------------------------------------------------------
        */
        Schema::table('paket_departure_prices', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM paket_departure_prices"))
                ->pluck('Key_name')
                ->toArray();

            if (!in_array('idx_paket_departure_prices_main', $indexes)) {
                $table->index(
                    ['paket_departure_id', 'price'],
                    'idx_paket_departure_prices_main'
                );
            }
        });
    }

    /**
     * ROLLBACK
     */
    public function down(): void
    {
        Schema::table('paket_departures', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM paket_departures"))
                ->pluck('Key_name')
                ->toArray();

            if (in_array('idx_paket_departures_main', $indexes)) {
                $table->dropIndex('idx_paket_departures_main');
            }
        });

        Schema::table('paket_departure_prices', function (Blueprint $table) {

            $indexes = collect(DB::select("SHOW INDEX FROM paket_departure_prices"))
                ->pluck('Key_name')
                ->toArray();

            if (in_array('idx_paket_departure_prices_main', $indexes)) {
                $table->dropIndex('idx_paket_departure_prices_main');
            }
        });
    }
};