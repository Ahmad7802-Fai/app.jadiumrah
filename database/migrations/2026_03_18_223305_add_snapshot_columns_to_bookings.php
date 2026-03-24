<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->decimal('original_price_snapshot', 15, 2)->nullable();
        $table->string('promo_label_snapshot')->nullable();
    });
}

public function down()
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn([
            'original_price_snapshot',
            'promo_label_snapshot'
        ]);
    });
}
};
