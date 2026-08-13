<?php

namespace App\Services;

use App\Models\TabunganMonthlyClosing;
use Illuminate\Support\Facades\DB;

class TabunganMonthlyClosingService
{
    public function close(int $bulan, int $tahun, int $userId): void
    {
        abort_if(
            TabunganMonthlyClosing::where(compact('bulan','tahun'))->exists(),
            409,
            'Periode sudah ditutup.'
        );

        DB::transaction(function () use ($bulan, $tahun, $userId) {

            $rekap = app(TabunganRekapService::class)
                ->monthly($bulan, $tahun);

            TabunganMonthlyClosing::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'total_saldo_awal'  => $rekap['summary']['saldo_awal'],
                'total_topup'       => $rekap['summary']['topup'],
                'total_debit'       => $rekap['summary']['debit'],
                'total_saldo_akhir' => $rekap['summary']['saldo_akhir'],
                'closed_at' => now(),
                'closed_by' => $userId,
            ]);
        });
    }
}
