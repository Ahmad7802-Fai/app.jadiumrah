<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\TabunganUmrah;
use App\Models\TabunganTransaksi;

class TabunganRekapService
{
    /**
     * Rekap BULANAN – SEMUA TABUNGAN
     */
    public function monthly(int $bulan, int $tahun): array
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $rows = [];
        $summary = [
            'saldo_awal'  => 0,
            'topup'       => 0,
            'debit'       => 0,
            'saldo_akhir' => 0,
        ];

        $tabungans = TabunganUmrah::with('jamaah')
            ->where('status', 'ACTIVE')
            ->orderBy('id')
            ->get();

        foreach ($tabungans as $tabungan) {

            // saldo sebelum bulan
            $saldoAwal = TabunganTransaksi::where('tabungan_id', $tabungan->id)
                ->where('created_at', '<', $start)
                ->sum('amount');

            // topup bulan ini
            $topup = TabunganTransaksi::where('tabungan_id', $tabungan->id)
                ->where('jenis', 'TOPUP')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            // debit bulan ini
            $debit = TabunganTransaksi::where('tabungan_id', $tabungan->id)
                ->whereIn('jenis', ['TARIK', 'TRANSFER_INVOICE'])
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount');

            $saldoAkhir = $saldoAwal + $topup - $debit;

            $rows[] = [
                'tabungan'     => $tabungan,
                'nomor_tabungan'=> $tabungan->nomor_tabungan,
                'jamaah'       => $tabungan->jamaah,
                'saldo_awal'   => $saldoAwal,
                'total_topup'  => $topup,
                'total_debit'  => $debit,
                'saldo_akhir'  => $saldoAkhir,
            ];

            $summary['saldo_awal']  += $saldoAwal;
            $summary['topup']       += $topup;
            $summary['debit']       += $debit;
            $summary['saldo_akhir'] += $saldoAkhir;
        }

        return compact('rows', 'summary', 'start', 'end');
    }

    /**
     * DETAIL MUTASI PER TABUNGAN
     */
    public function detail(TabunganUmrah $tabungan, int $bulan, int $tahun)
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $mutasi = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        return compact('mutasi', 'start', 'end');
    }
}
