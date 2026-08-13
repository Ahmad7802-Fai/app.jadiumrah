<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Services\TabunganClosingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabunganClosingController extends Controller
{
    /**
     * ======================================================
     * CLOSE MONTH
     * Role: KEUANGAN / OWNER
     * ======================================================
     */
    public function close(Request $request)
    {
        TabunganClosingService::close(
            $request->bulan,
            $request->tahun,
            auth()->id()
        );

        return back()->with('success', 'Bulan berhasil ditutup.');
    }

    public function open(Request $request)
    {
        abort_unless(
            auth()->user()->role === 'SUPERADMIN',
            403
        );

        TabunganClosingService::open(
            $request->bulan,
            $request->tahun
        );

        return back()->with('success', 'Bulan berhasil dibuka.');
    }

}
