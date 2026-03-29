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
Schema::table('bookings', function (Blueprint $table) {

    $table->foreignId('voucher_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->decimal('voucher_discount', 15, 2)
        ->default(0);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
