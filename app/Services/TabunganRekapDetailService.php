<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\TabunganUmrah;
use App\Models\TabunganTransaksi;

class TabunganRekapDetailService
{
    public function monthlyDetail(int $tabunganId, int $tahun, int $bulan): array
    {
        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $tabungan = TabunganUmrah::with('jamaah')->findOrFail($tabunganId);

        // === SALDO AWAL
        $saldoAwal = TabunganTransaksi::where('tabungan_id', $tabunganId)
            ->where('created_at', '<', $start)
            ->sum('amount');

        $saldoBerjalan = $saldoAwal;

        // === MUTASI BULAN INI
        $mutasi = TabunganTransaksi::where('tabungan_id', $tabunganId)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get()
            ->map(function ($trx) use (&$saldoBerjalan) {

                $isDebit = in_array($trx->jenis, ['TARIK', 'TRANSFER_INVOICE']);
                $nominal = (int) $trx->amount;

                $saldoBerjalan += $isDebit ? -$nominal : $nominal;

                return [
                    'tanggal' => $trx->created_at,
                    'jenis'   => $trx->jenis,
                    'debit'   => $isDebit ? $nominal : 0,
                    'kredit'  => $isDebit ? 0 : $nominal,
                    'saldo'   => $saldoBerjalan,
                    'ref'     => $trx->reference ?? '-',
                ];
            });

        return [
            'tabungan'    => $tabungan,
            'periode'     => $start->translatedFormat('F Y'),
            'saldo_awal'  => $saldoAwal,
            'mutasi'      => $mutasi,
            'saldo_akhir' => $saldoBerjalan,
        ];
    }
}
