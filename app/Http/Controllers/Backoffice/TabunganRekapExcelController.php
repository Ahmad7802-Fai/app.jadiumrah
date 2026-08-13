<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Exports\TabunganRekapExport;
use Maatwebsite\Excel\Facades\Excel;

class TabunganRekapExcelController extends Controller
{
    public function export(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        abort_if($bulan < 1 || $bulan > 12, 400, 'Bulan tidak valid.');
        abort_if($tahun < 2020 || $tahun > now()->year + 1, 400, 'Tahun tidak valid.');

        $periode = Carbon::create($tahun, $bulan, 1)
            ->translatedFormat('F-Y');

        return Excel::download(
            new TabunganRekapExport($bulan, $tahun),
            'Rekap-Tabungan-' . $periode . '.xlsx'
        );
    }
}
