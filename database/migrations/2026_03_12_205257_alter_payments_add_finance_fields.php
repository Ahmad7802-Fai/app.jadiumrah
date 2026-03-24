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
        Schema::table('payments', function (Blueprint $table) {

            $table->string('reference_number',100)
                ->nullable()
                ->after('payment_code');

            $table->enum('channel',[
                'website',
                'agent',
                'admin',
                'gateway'
            ])->default('website')
              ->after('method');

            $table->decimal('fee_amount',15,2)
                ->default(0)
                ->after('amount');

            $table->decimal('net_amount',15,2)
                ->nullable()
                ->after('fee_amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->dropColumn([
                'reference_number',
                'channel',
                'fee_amount',
                'net_amount'
            ]);

        });
    }
};