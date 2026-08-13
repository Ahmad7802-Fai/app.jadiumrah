<?php

namespace App\Services;

use App\Models\TabunganMonthlyClosing;
use App\Models\TabunganUmrah;
use Illuminate\Support\Facades\DB;

class TabunganClosingService
{
    /* ================================
       SINGLE SOURCE OF TRUTH
    ================================= */

    public static function isLocked(int $bulan, int $tahun): bool
    {
        return TabunganMonthlyClosing::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();
    }

    public static function close(int $bulan, int $tahun, int $userId): void
    {
        abort_if(
            self::isLocked($bulan, $tahun),
            409,
            'Bulan sudah ditutup.'
        );

        TabunganMonthlyClosing::create([
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'total_saldo_awal'  => TabunganUmrah::sum('saldo'),
            'total_topup'       => 0,
            'total_debit'       => 0,
            'total_saldo_akhir' => TabunganUmrah::sum('saldo'),
            'closed_by'         => $userId,
            'closed_at'         => now(),
        ]);
    }

    public static function open(int $bulan, int $tahun): void
    {
        TabunganMonthlyClosing::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete(); // 🔥 HARD UNLOCK
    }
}
