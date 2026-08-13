<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\TabunganUmrah;
use App\Models\TabunganTransaksi;
use App\Services\TabunganRekapService;

use PDF;

class TabunganRekapDetailPdfController extends Controller
{
    public function export(
        TabunganUmrah $tabungan,
        Request $request,
        TabunganRekapService $service
    ) {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        abort_if($bulan < 1 || $bulan > 12, 400);
        abort_if($tahun < 2020 || $tahun > now()->year + 1, 400);

        $start = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $end   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        /* ================= SALDO AWAL ================= */
        $saldoAwal = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->where('created_at', '<', $start)
            ->sum('amount');

        /* ================= MUTASI ================= */
        $mutasi = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        /* ================= TOTAL ================= */
        $totalKredit = $mutasi
            ->where('jenis', 'TOPUP')
            ->sum('amount');

        $totalDebit = $mutasi
            ->whereIn('jenis', ['TARIK', 'TRANSFER_INVOICE'])
            ->sum('amount');

        $saldoAkhir = $saldoAwal + $totalKredit - $totalDebit;

        $periode = Carbon::create($tahun, $bulan, 1)
            ->translatedFormat('F Y');

        /* ================= PDF ================= */
        $pdf = PDF::loadView(
            'keuangan.tabungan.rekap.detail-pdf',
            compact(
                'tabungan',
                'mutasi',
                'saldoAwal',
                'totalKredit',
                'totalDebit',
                'saldoAkhir',
                'periode'
            )
        )->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Mutasi-Tabungan-'.$tabungan->nomor_tabungan.'-'.$periode.'.pdf'
        );
    }
}
