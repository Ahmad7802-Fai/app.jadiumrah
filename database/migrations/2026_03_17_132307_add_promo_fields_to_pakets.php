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
        Schema::table('pakets', function (Blueprint $table) {

            $table->string('promo_label')->nullable();
            $table->integer('promo_value')->nullable(); // persen atau nominal
            $table->string('promo_type')->nullable(); // discount / cashback
            $table->timestamp('promo_expires_at')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            //
        });
    }
};
