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
    DB::statement("
        ALTER TABLE bookings 
        MODIFY status ENUM(
            'draft',
            'waiting_payment',
            'confirmed',
            'expired',
            'cancelled'
        ) NOT NULL DEFAULT 'draft'
    ");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
