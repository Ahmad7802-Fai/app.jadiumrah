<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\TabunganRekapService;
use App\Models\TabunganUmrah;

class TabunganRekapPdfController extends Controller
{
    protected TabunganRekapService $rekapService;

    public function __construct(TabunganRekapService $rekapService)
    {
        $this->rekapService = $rekapService;
    }

    /* ======================================================
     | EXPORT PDF – REKAP BULANAN TABUNGAN
     | Read-only | Audit Ready
     ====================================================== */
    public function export(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        abort_if($bulan < 1 || $bulan > 12, 400, 'Bulan tidak valid.');
        abort_if($tahun < 2020 || $tahun > now()->year + 1, 400, 'Tahun tidak valid.');

        $periodeText = Carbon::create($tahun, $bulan, 1)
            ->translatedFormat('F Y');

        /* ================= SERVICE ================= */
        $data = $this->rekapService->monthly($bulan, $tahun);

        $rekap   = $data['rows'];      // ✅ BARIS
        $summary = $data['summary'];   // ✅ TOTAL

        /* ================= PDF ================= */
        $pdf = Pdf::loadView(
            'keuangan.tabungan.rekap.pdf',
            compact('rekap', 'summary', 'periodeText')
        )->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Rekap-Tabungan-' . $periodeText . '.pdf'
        );
    }

}
