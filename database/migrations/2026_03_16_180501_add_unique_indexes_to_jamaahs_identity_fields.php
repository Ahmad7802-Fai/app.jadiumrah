<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateNik = DB::table('jamaahs')
            ->selectRaw('TRIM(nik) as value, COUNT(*) as total')
            ->whereNotNull('nik')
            ->whereRaw("TRIM(nik) != ''")
            ->groupByRaw('TRIM(nik)')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicateNik) {
            throw new \RuntimeException(
                "Gagal menambahkan unique index jamaahs.nik karena masih ada data duplikat: {$duplicateNik->value}"
            );
        }

        $duplicatePassport = DB::table('jamaahs')
            ->selectRaw('UPPER(TRIM(passport_number)) as value, COUNT(*) as total')
            ->whereNotNull('passport_number')
            ->whereRaw("TRIM(passport_number) != ''")
            ->groupByRaw('UPPER(TRIM(passport_number))')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicatePassport) {
            throw new \RuntimeException(
                "Gagal menambahkan unique index jamaahs.passport_number karena masih ada data duplikat: {$duplicatePassport->value}"
            );
        }

        Schema::table('jamaahs', function (Blueprint $table) {
            $table->unique('nik', 'jamaahs_nik_unique');
            $table->unique('passport_number', 'jamaahs_passport_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('jamaahs', function (Blueprint $table) {
            $table->dropUnique('jamaahs_nik_unique');
            $table->dropUnique('jamaahs_passport_number_unique');
        });
    }
};