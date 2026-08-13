<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Services\TabunganMonthlyClosingService;
use Illuminate\Http\Request;

class TabunganMonthlyClosingController extends Controller
{
    public function store(
        Request $request,
        TabunganMonthlyClosingService $service
    ) {
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        abort_if($bulan < 1 || $bulan > 12, 400);
        abort_if($tahun < 2020, 400);

        $service->close($bulan, $tahun, auth()->id());

        return back()->with('success', 'Bulan berhasil ditutup.');
    }
}
