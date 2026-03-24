<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->string('booking_code')
                  ->nullable()
                  ->unique()
                  ->after('id');

            $table->string('invoice_number')
                  ->nullable()
                  ->unique()
                  ->after('booking_code');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {

            $table->dropColumn([
                'booking_code',
                'invoice_number'
            ]);
        });
    }
};