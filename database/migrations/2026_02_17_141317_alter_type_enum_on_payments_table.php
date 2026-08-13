<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE payments 
            MODIFY COLUMN type 
            ENUM(
                'dp',
                'cicilan',
                'pelunasan',
                'add_on',
                'upgrade',
                'adjustment'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE payments 
            MODIFY COLUMN type 
            ENUM(
                'dp',
                'pelunasan',
                'add_on',
                'upgrade',
                'lainnya'
            ) NOT NULL
        ");
    }
};