<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_departure_prices', function (Blueprint $table) {

            // 🔥 PROMO TYPE (percent / fixed)
            $table->enum('promo_type', ['percent', 'fixed'])
                ->nullable()
                ->after('price');

            // 🔥 NILAI PROMO
            $table->integer('promo_value')
                ->nullable()
                ->after('promo_type');

            // 🔥 LABEL PROMO (contoh: Diskon Ramadhan)
            $table->string('promo_label')
                ->nullable()
                ->after('promo_value');

            // 🔥 EXPIRED PROMO
            $table->timestamp('promo_expires_at')
                ->nullable()
                ->after('promo_label');
        });
    }

    public function down(): void
    {
        Schema::table('paket_departure_prices', function (Blueprint $table) {

            $table->dropColumn([
                'promo_type',
                'promo_value',
                'promo_label',
                'promo_expires_at'
            ]);
        });
    }
};