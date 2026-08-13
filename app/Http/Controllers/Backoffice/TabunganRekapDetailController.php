<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\TabunganUmrah;
use App\Models\TabunganTransaksi;

class TabunganRekapDetailController extends Controller
{
    /**
     * ======================================================
     * DETAIL MUTASI TABUNGAN (DRILL-DOWN)
     * Role: KEUANGAN / OWNER / AUDITOR
     * Read-only, Audit Safe
     * ======================================================
     */
    public function index(Request $request, TabunganUmrah $tabungan)
    {
        /* ================= VALIDASI PERIODE ================= */
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        abort_if($bulan < 1 || $bulan > 12, 400, 'Bulan tidak valid.');

        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        /* ================= LOAD RELASI ================= */
        $tabungan->load('jamaah');

        /* ================= SALDO AWAL ================= */
        $saldoAwal = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->where('created_at', '<', $start)
            ->sum('amount');

        /* ================= MUTASI ================= */
        $mutasi = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'ASC')
            ->get();

        /* ================= SALDO BERJALAN ================= */
        $runningSaldo = $saldoAwal;

        $rows = $mutasi->map(function ($trx) use (&$runningSaldo) {

            if (in_array($trx->jenis, ['TOPUP'])) {
                $runningSaldo += $trx->amount;
                $debit = 0;
                $kredit = $trx->amount;
            } else {
                $runningSaldo -= $trx->amount;
                $debit = $trx->amount;
                $kredit = 0;
            }

            return [
                'tanggal'      => $trx->created_at,
                'keterangan'   => $trx->keterangan,
                'debit'        => $debit,
                'kredit'       => $kredit,
                'saldo'        => $runningSaldo,
                'reference'    => $trx->reference_type,
                'reference_id' => $trx->reference_id,
            ];
        });

        /* ================= SUMMARY ================= */
        $summary = [
            'saldo_awal'  => $saldoAwal,
            'total_kredit'=> $rows->sum('kredit'),
            'total_debit' => $rows->sum('debit'),
            'saldo_akhir' => $runningSaldo,
        ];

        return view('keuangan.tabungan.rekap.detail', [
            'tabungan' => $tabungan,
            'rows'     => $rows,
            'summary'  => $summary,
            'bulan'    => $bulan,
            'tahun'    => $tahun,
        ]);
    }
}
