<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->string('seat_number', 10)
                  ->nullable()
                  ->after('passport_number');
        });
    }

    public function down(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropColumn('seat_number');
        });
    }
};