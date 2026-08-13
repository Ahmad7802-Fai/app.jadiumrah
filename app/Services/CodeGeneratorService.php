<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CodeGeneratorService
{
    public function generate(
        string $prefix,
        string $entity,
        int $pad = 5,
        bool $yearly = true
    ): string {

        $year = now()->year;

        $key = $yearly
            ? "{$entity}_{$year}"
            : $entity;

        $number = DB::transaction(function () use ($key) {

            $row = DB::table('code_counters')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if (!$row) {

                DB::table('code_counters')->insert([
                    'key'         => $key,
                    'last_number' => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                return 1;
            }

            $newNumber = $row->last_number + 1;

            DB::table('code_counters')
                ->where('key', $key)
                ->update([
                    'last_number' => $newNumber,
                    'updated_at'  => now(),
                ]);

            return $newNumber;
        });

        if ($yearly) {
            return "{$prefix}-{$year}-" .
                str_pad($number, $pad, '0', STR_PAD_LEFT);
        }

        // 🔥 include entity date when not yearly
        return "{$prefix}-" . now()->format('Ymd') . "-" .
            str_pad($number, $pad, '0', STR_PAD_LEFT);
            }

}