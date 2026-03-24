<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE bookings 
            MODIFY status ENUM(
                'draft',
                'confirmed',
                'cancelled',
                'expired'
            ) NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE bookings 
            MODIFY status ENUM(
                'draft',
                'confirmed',
                'cancelled'
            ) NOT NULL DEFAULT 'draft'
        ");
    }
};