<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE jamaahs
            SET passport_number = NULL
            WHERE passport_number IS NOT NULL
              AND TRIM(passport_number) = ''
        ");

        DB::statement("
            UPDATE jamaahs
            SET email = NULL
            WHERE email IS NOT NULL
              AND TRIM(email) = ''
        ");

        DB::statement("
            UPDATE jamaahs
            SET nik = NULL
            WHERE nik IS NOT NULL
              AND TRIM(nik) = ''
        ");

        DB::statement("
            UPDATE jamaahs
            SET passport_number = UPPER(TRIM(passport_number))
            WHERE passport_number IS NOT NULL
        ");

        DB::statement("
            UPDATE jamaahs
            SET email = LOWER(TRIM(email))
            WHERE email IS NOT NULL
        ");

        DB::statement("
            UPDATE jamaahs
            SET nik = TRIM(nik)
            WHERE nik IS NOT NULL
        ");
    }

    public function down(): void
    {
        // Tidak di-reverse karena ini normalisasi data existing.
    }
};