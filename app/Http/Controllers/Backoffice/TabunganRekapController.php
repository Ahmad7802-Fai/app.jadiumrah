<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TabunganRekapService;
use App\Services\TabunganClosingService;

class TabunganRekapController extends Controller
{
    protected TabunganRekapService $rekapService;

    public function __construct(TabunganRekapService $rekapService)
    {
        $this->rekapService = $rekapService;
    }

    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $q     = trim($request->get('q'));

        abort_if($bulan < 1 || $bulan > 12, 400);
        abort_if($tahun < 2020 || $tahun > now()->year + 1, 400);

        $data = $this->rekapService->monthly($bulan, $tahun);

        $rows = collect($data['rows']);

        if ($q) {
            $rows = $rows->filter(fn ($r) =>
                str_contains(
                    strtolower($r['jamaah']->nama_lengkap ?? ''),
                    strtolower($q)
                )
            )->values();
        }

        $summary = [
            'saldo_awal'  => $rows->sum('saldo_awal'),
            'topup'       => $rows->sum('total_topup'),
            'debit'       => $rows->sum('total_debit'),
            'saldo_akhir' => $rows->sum('saldo_akhir'),
        ];

        // ✅ SINGLE SOURCE OF TRUTH
        $isLocked = TabunganClosingService::isLocked($bulan, $tahun);

        return view('keuangan.tabungan.rekap.index', [
            'rekap'    => $rows,
            'summary'  => $summary,
            'bulan'    => $bulan,
            'tahun'    => $tahun,
            'q'        => $q,
            'isLocked' => $isLocked,
        ]);
    }
}


// namespace App\Http\Controllers\Backoffice;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

// use App\Services\TabunganRekapService;
// use App\Services\TabunganClosingService;
// use Carbon\Carbon;

// public function index(Request $request)
// {
//     $bulan = (int) $request->get('bulan', now()->month);
//     $tahun = (int) $request->get('tahun', now()->year);
//     $q     = trim($request->get('q'));

//     abort_if($bulan < 1 || $bulan > 12, 400);
//     abort_if($tahun < 2020 || $tahun > now()->year + 1, 400);

//     $data = $this->rekapService->monthly($bulan, $tahun);

//     $rows = collect($data['rows']);

//     if ($q) {
//         $rows = $rows->filter(fn ($r) =>
//             str_contains(
//                 strtolower($r['jamaah']->nama_lengkap ?? ''),
//                 strtolower($q)
//             )
//         )->values();
//     }

//     $summary = [
//         'saldo_awal'  => $rows->sum('saldo_awal'),
//         'topup'       => $rows->sum('total_topup'),
//         'debit'       => $rows->sum('total_debit'),
//         'saldo_akhir' => $rows->sum('saldo_akhir'),
//     ];

//     $isLocked = TabunganClosingService::isLocked(
//         Carbon::create($tahun, $bulan, 1)
//     );

//     return view('keuangan.tabungan.rekap.index', [
//         'rekap'    => $rows,
//         'summary'  => $summary,
//         'bulan'    => $bulan,
//         'tahun'    => $tahun,
//         'q'        => $q,
//         'isLocked' => $isLocked,
//     ]);
// }

// class TabunganRekapController extends Controller
// {
//     protected TabunganRekapService $rekapService;

//     public function __construct(TabunganRekapService $rekapService)
//     {
//         $this->rekapService = $rekapService;
//     }

//     /**
//      * ======================================================
//      * INDEX – REKAP BULANAN TABUNGAN
//      * Role: KEUANGAN / OWNER / AUDITOR
//      * Read-only, Audit Safe
//      * ======================================================
//      */
//     public function index(Request $request)
//     {
//         /* ================= VALIDASI INPUT ================= */
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);
//         $q     = trim($request->get('q')); // 🔍 keyword pencarian

//         abort_if($bulan < 1 || $bulan > 12, 400, 'Bulan tidak valid.');
//         abort_if($tahun < 2020 || $tahun > now()->year + 1, 400, 'Tahun tidak valid.');

//         /* ================= SERVICE ================= */
//         $data = $this->rekapService->monthly($bulan, $tahun);

//         /* ================= FILTER NAMA JAMAAH ================= */
//         $rows = collect($data['rows']);

//         if ($q) {
//             $rows = $rows->filter(function ($r) use ($q) {
//                 return str_contains(
//                     strtolower($r['jamaah']->nama_lengkap ?? ''),
//                     strtolower($q)
//                 );
//             })->values();
//         }

//         /* ================= HITUNG ULANG SUMMARY ================= */
//         $summary = [
//             'saldo_awal'  => $rows->sum('saldo_awal'),
//             'topup'       => $rows->sum('total_topup'),
//             'debit'       => $rows->sum('total_debit'),
//             'saldo_akhir' => $rows->sum('saldo_akhir'),
//         ];

//         /* ================= VIEW ================= */
//         return view('keuangan.tabungan.rekap.index', [
//             'rekap'   => $rows,        // ← HASIL FILTER
//             'summary' => $summary,     // ← TOTAL SESUAI FILTER
//             'bulan'   => $bulan,
//             'tahun'   => $tahun,
//             'q'       => $q,           // ← KIRIM KE VIEW
//         ]);
//     }

// }
