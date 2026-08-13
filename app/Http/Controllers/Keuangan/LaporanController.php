<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\Keuangan\LaporanKeuanganService;
use App\Services\Keuangan\TripProfitService;
use App\Models\PaketMaster;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CompanyProfile;
use Carbon\Carbon;

// EXPORTS
use App\Exports\CashflowExport;
use App\Exports\LaporanPnlExport;

class LaporanController extends Controller
{
    public function __construct(
        protected LaporanKeuanganService $keuanganService,
        protected TripProfitService $tripProfitService
    ) {}

    /* =====================================================
     | DASHBOARD KEUANGAN (SUMMARY)
     ===================================================== */
    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));

        $summary = $this->keuanganService->summaryBulanan($periode);
        $trend   = $this->keuanganService->trend6Bulan();

        return view('keuangan.laporan.index', [
            'periode' => $periode,

            // ================= SUMMARY =================
            'pendapatanJamaah'  => $summary['pendapatan_jamaah'],
            'pendapatanLayanan' => $summary['pendapatan_layanan'],
            'totalPendapatan'   => $summary['total_pendapatan'],

            'biayaTrip'         => $summary['biaya_trip'],
            'biayaOperasional'  => $summary['biaya_operasional'],
            'biayaVendor'       => $summary['biaya_vendor'],
            'biayaMarketing'    => $summary['biaya_marketing'],
            'totalPengeluaran'  => $summary['total_pengeluaran'],

            'labaBersih'        => $summary['laba_bersih'],

            // ================= TREND =================
            'months'        => $trend['months'],
            'trendRevenue'  => $trend['revenueTrend'],
            'trendExpense'  => $trend['expenseTrend'],
        ]);
    }

    /* =====================================================
     | PNL BULANAN
     ===================================================== */
public function monthlyPnl(Request $request)
{
    $bulan = (int) $request->get('bulan', now()->month);
    $tahun = (int) $request->get('tahun', now()->year);

    $bulanNama = Carbon::create($tahun, $bulan, 1)
        ->translatedFormat('F');

    $pnl = $this->keuanganService->pnlBulanan($bulan, $tahun);

    return view('keuangan.laporan.pnl', [
        'bulan'     => $bulan,
        'tahun'     => $tahun,
        'bulanNama' => $bulanNama,

        // ================= REVENUE =================
        'revenueJamaah'  => $pnl['revenue_jamaah'],
        'revenueLayanan' => $pnl['revenue_layanan'],
        'totalRevenue'   => $pnl['total_revenue'],

        // ================= HPP =================
        'tripExpenses'   => $pnl['biaya_trip'],
        'vendorExpenses' => $pnl['biaya_vendor'],
        'hpp'            => $pnl['total_hpp'],

        // ================= OPEX =================
        'operational'    => $pnl['biaya_operasional'],
        'marketing'      => $pnl['biaya_marketing'],

        // ================= PROFIT =================
        'grossProfit'    => $pnl['gross_profit'],
        'netProfit'      => $pnl['net_profit'],
    ]);
}

    /* =====================================================
     | TRIP PROFIT
     ===================================================== */
    public function tripProfit(Request $request)
    {
        $paketId = $request->integer('paket_id');

        return view('keuangan.laporan.trip_profit', [
            'keberangkatan' => $this->tripProfitService->list($paketId),
            'paketList'     => PaketMaster::orderBy('nama_paket')->get(),
            'paketId'       => $paketId,
        ]);
    }
    
    /* =====================================================
    | CASHFLOW — VIEW
    ===================================================== */
    public function cashflow(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $cashflow = $this->keuanganService->cashflowBulanan($bulan, $tahun);
        $trend    = $this->keuanganService->trendCashflow6Bulan();

        return view('keuangan.laporan.cashflow', [
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'bulanNama'  => \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F'),

            // ✅ SUMMARY (AMBIL DARI STRUCTURE SERVICE)
            'totalCashIn'  => $cashflow['cashIn']['total'],
            'totalCashOut' => $cashflow['cashOut']['total'],
            'netCashflow'  => $cashflow['netCashflow'],

            // TABLE
            'cashIn'   => $cashflow['cashIn'],
            'cashOut'  => $cashflow['cashOut'],

            // TREND
            'months'   => $trend['months'],
            'trendIn'  => $trend['cashIn'],
            'trendOut' => $trend['cashOut'],
        ]);
    }


    /* =====================================================
    | EXPORT PDF — CASHFLOW
    ===================================================== */

    public function cashflowPdf(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $cashflow = $this->keuanganService->cashflowBulanan($bulan, $tahun);

        $company = CompanyProfile::where('is_active', 1)->first();

        $pdf = Pdf::loadView('keuangan.laporan.pdf.cashflow-f4', [
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'bulanNama'    => \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F'),

            // COMPANY
            'company'      => $company,

            // SUMMARY
            'totalCashIn'  => $cashflow['cashIn']['total'],
            'totalCashOut' => $cashflow['cashOut']['total'],
            'netCashflow'  => $cashflow['netCashflow'],

            // DETAIL
            'cashIn'       => $cashflow['cashIn'],
            'cashOut'      => $cashflow['cashOut'],
        ])->setPaper('F4', 'portrait');

        return $pdf->stream("Cashflow-{$bulan}-{$tahun}.pdf");
    }

    /* =====================================================
    | EXPORT PDF — PNL
    ===================================================== */
public function pnlPdf(Request $request)
{
    $bulan = (int) $request->get('bulan', now()->month);
    $tahun = (int) $request->get('tahun', now()->year);

    $bulanNama = Carbon::create($tahun, $bulan, 1)
        ->translatedFormat('F');

    $pnl = $this->keuanganService->pnlBulanan($bulan, $tahun);

    $company = \App\Models\CompanyProfile::where('is_active', 1)->first();

    $viewData = [
        // ================= META =================
        'bulan'     => $bulan,
        'tahun'     => $tahun,
        'bulanNama' => $bulanNama,
        'company'   => $company,

        // ================= REVENUE =================
        'revenueJamaah'  => $pnl['revenue_jamaah'],
        'revenueLayanan' => $pnl['revenue_layanan'],
        'totalRevenue'   => $pnl['total_revenue'],

        // ================= HPP =================
        'tripExpenses'   => $pnl['biaya_trip'],
        'vendorExpenses' => $pnl['biaya_vendor'],
        'hpp'            => $pnl['total_hpp'],

        // ================= OPEX =================
        'operational'    => $pnl['biaya_operasional'],
        'marketing'      => $pnl['biaya_marketing'],

        // ================= PROFIT =================
        'grossProfit'    => $pnl['gross_profit'],
        'netProfit'      => $pnl['net_profit'],
    ];

    return Pdf::loadView(
        'keuangan.laporan.pdf.pnl-f4',
        $viewData
    )->setPaper('F4', 'portrait')
     ->stream("PNL-{$bulan}-{$tahun}.pdf");
}


    /* =====================================================
     | EXPORT EXCEL — PNL
     ===================================================== */
    public function pnlExcel(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $viewData = $this->mapPnlViewData($bulan, $tahun);

        return Excel::download(
            new LaporanPnlExport($viewData),
            "PNL-{$bulan}-{$tahun}.xlsx"
        );
    }

    /* =====================================================
     | PRIVATE METHOD: MAP PNL VIEW DATA
     ===================================================== */
    private function mapPnlViewData(int $bulan, int $tahun): array
    {
        $bulanNama = Carbon::create($tahun, $bulan, 1)
            ->translatedFormat('F');

        $pnl = $this->keuanganService->pnlBulanan($bulan, $tahun);

        return [
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'bulanNama' => $bulanNama,

            // REVENUE
            'revenueJamaah'  => $pnl['totalRevenueJamaah'],
            'revenueLayanan' => $pnl['totalServiceRevenue'],
            'totalRevenue'   => $pnl['totalRevenueAll'],

            // EXPENSE
            'tripExpenses'   => $pnl['totalTripExpenses'],
            'vendorExpenses' => $pnl['totalVendorExpense'],
            'operational'    => $pnl['totalOperational'],

            // CALCULATED
            'hpp'         => $pnl['totalTripExpenses'] + $pnl['totalVendorExpense'],
            'grossProfit' => $pnl['totalRevenueAll']
                            - ($pnl['totalTripExpenses'] + $pnl['totalVendorExpense']),
            'netProfit'   => $pnl['netAll'],
        ];
    }

}


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use App\Services\Keuangan\LaporanKeuanganService;
// use App\Services\Keuangan\TripProfitService;
// use App\Models\PaketMaster;
// use Illuminate\Http\Request;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Maatwebsite\Excel\Facades\Excel;

// // EXPORTS
// use App\Exports\CashflowExport;
// use App\Exports\LaporanPnlExport;

// class LaporanController extends Controller
// {
//     public function __construct(
//         protected LaporanKeuanganService $service
//     ) {}

//     /* =====================================================
//      | DASHBOARD KEUANGAN (SUMMARY)
//      ===================================================== */
//     public function index(Request $request)
//     {
//         $periode = $request->get('periode', now()->format('Y-m'));

//         $summary = $this->service->summaryBulanan($periode);
//         $trend   = $this->service->trend6Bulan();

//         return view('keuangan.laporan.index', [
//             'periode' => $periode,

//             // ================= SUMMARY =================
//             'pendapatanJamaah'  => $summary['pendapatan_jamaah'],
//             'pendapatanLayanan' => $summary['pendapatan_layanan'],
//             'totalPendapatan'   => $summary['total_pendapatan'],

//             'biayaTrip'         => $summary['biaya_trip'],
//             'biayaOperasional'  => $summary['biaya_operasional'],
//             'biayaVendor'       => $summary['biaya_vendor'],
//             'biayaMarketing'    => $summary['biaya_marketing'],
//             'totalPengeluaran'  => $summary['total_pengeluaran'],

//             'labaBersih'        => $summary['laba_bersih'],

//             // ================= TREND =================
//             'months'        => $trend['months'],
//             'trendRevenue'  => $trend['revenueTrend'],
//             'trendExpense'  => $trend['expenseTrend'],
//         ]);
//     }

//     /* =====================================================
//      | PNL BULANAN
//      ===================================================== */
//     public function monthlyPnl(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->pnlBulanan($bulan, $tahun);

//         return view('keuangan.laporan.pnl', array_merge(
//             compact('bulan', 'tahun'),
//             $data
//         ));
//     }

//     /* =====================================================
//     | TRIP PROFIT
//     ===================================================== */


//     public function tripProfit(Request $request, TripProfitService $service)
//     {
//         $paketId = $request->integer('paket_id');

//         return view('keuangan.laporan.trip_profit', [
//             'keberangkatan' => $service->list($paketId),
//             'paketList'     => PaketMaster::orderBy('nama_paket')->get(),
//             'paketId'       => $paketId,
//         ]);
//     }

//     /* =====================================================
//      | CASHFLOW VIEW
//      ===================================================== */
//     public function cashflow(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->cashflowBulanan($bulan, $tahun);
//         $trend = $this->service->trend6Bulan();

//         return view('keuangan.laporan.cashflow', array_merge(
//             compact('bulan', 'tahun'),
//             $data,
//             [
//                 'months'        => $trend['months'],
//                 'trendRevenue'  => $trend['revenueTrend'],
//                 'trendExpense'  => $trend['expenseTrend'],
//             ]
//         ));
//     }

//     /* =====================================================
//      | EXPORT PDF — CASHFLOW
//      ===================================================== */
//     public function cashflowPdf(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->cashflowBulanan($bulan, $tahun);

//         $pdf = Pdf::loadView(
//             'keuangan.laporan.pdf.cashflow-f4',
//             array_merge(compact('bulan', 'tahun'), $data)
//         )->setPaper('F4', 'portrait');

//         return $pdf->stream("Cashflow-{$bulan}-{$tahun}.pdf");
//     }

//     /* =====================================================
//      | EXPORT EXCEL — CASHFLOW
//      ===================================================== */
//     public function cashflowExcel(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->cashflowBulanan($bulan, $tahun);

//         return Excel::download(
//             new CashflowExport($data),
//             "Cashflow-{$bulan}-{$tahun}.xlsx"
//         );
//     }

//     /* =====================================================
//      | EXPORT PDF — PNL
//      ===================================================== */
//     public function pnlPdf(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->pnlBulanan($bulan, $tahun);

//         $pdf = Pdf::loadView(
//             'keuangan.laporan.pdf.pnl-f4',
//             array_merge(compact('bulan', 'tahun'), $data)
//         )->setPaper('F4', 'portrait');

//         return $pdf->stream("PNL-{$bulan}-{$tahun}.pdf");
//     }

//     /* =====================================================
//      | EXPORT EXCEL — PNL
//      ===================================================== */
//     public function pnlExcel(Request $request)
//     {
//         $bulan = (int) $request->get('bulan', now()->month);
//         $tahun = (int) $request->get('tahun', now()->year);

//         $data = $this->service->pnlBulanan($bulan, $tahun);

//         return Excel::download(
//             new LaporanPnlExport($data),
//             "PNL-{$bulan}-{$tahun}.xlsx"
//         );
//     }
// }

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// // MODELS
// use App\Models\Payments;
// use App\Models\TripExpenses;
// use App\Models\OperationalExpenses;
// use App\Models\VendorPayment;
// use App\Models\PaketMaster;
// use App\Models\Keberangkatan;

// // PDF & EXCEL
// use Barryvdh\DomPDF\Facade\Pdf;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\LaporanPnlExport;
// use App\Exports\CashflowExport; // 

// class LaporanController extends Controller
// {
//     /* ==========================================================
//        CONTROLLER UTILITY
//     ========================================================== */
//     protected function operationalModel()
//     {
//         return class_exists(\App\Models\OperationalExpense::class)
//             ? \App\Models\OperationalExpense::class
//             : \App\Models\OperationalExpenses::class;
//     }


//     /* ==========================================================
//        DASHBOARD KEUANGAN
//     ========================================================== */
//     public function index(Request $request)
//     {
//         $periode = $request->get('periode', now()->format('Y-m'));

//         $year  = (int) substr($periode, 0, 4);
//         $month = (int) substr($periode, 5, 2);

//         /* --------------------------
//            Revenue Jamaah
//         ---------------------------*/
//         $revJamaah = Payments::where('status','valid')
//             ->whereYear('tanggal_bayar', $year)
//             ->whereMonth('tanggal_bayar', $month)
//             ->sum('jumlah');

//         /* --------------------------
//            Revenue Layanan B2B
//         ---------------------------*/
//         $revLayanan = DB::table('layanan_payments')
//             ->where('status', 'approved')
//             ->whereYear('created_at', $year)
//             ->whereMonth('created_at', $month)
//             ->sum('amount');

//         /* --------------------------
//            Expenses
//         ---------------------------*/
//         $expTrip = TripExpenses::whereYear('tanggal', $year)
//             ->whereMonth('tanggal', $month)
//             ->sum('jumlah');

//         $operationalClass = $this->operationalModel();
//         $expOp = $operationalClass::whereYear('tanggal', $year)
//             ->whereMonth('tanggal', $month)
//             ->sum('jumlah');

//         $expVendor = VendorPayment::whereYear('payment_date', $year)
//             ->whereMonth('payment_date', $month)
//             ->sum('amount');

//         /* --------------------------
//            Summary
//         ---------------------------*/
//         $totalRevenue = $revJamaah + $revLayanan;
//         $totalExpense = $expTrip + $expOp + $expVendor;
//         $netAll = $totalRevenue - $totalExpense;

//         /* --------------------------
//            Trend 6 Months
//         ---------------------------*/
//         $months = [];
//         $trendRevenue = [];
//         $trendExpense = [];

//         for ($i = 5; $i >= 0; $i--) {

//             $m = Carbon::now()->subMonths($i);

//             $months[] = $m->format('M Y');

//             $mRevJamaah = Payments::where('status','valid')
//                 ->whereYear('tanggal_bayar', $m->year)
//                 ->whereMonth('tanggal_bayar', $m->month)
//                 ->sum('jumlah');

//             $mRevLayanan = DB::table('layanan_payments')
//                 ->where('status', 'approved')
//                 ->whereYear('created_at', $m->year)
//                 ->whereMonth('created_at', $m->month)
//                 ->sum('amount');

//             $mTrip = TripExpenses::whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah');

//             $mOp = OperationalExpenses::whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah');

//             $mVendor = VendorPayment::whereYear('payment_date', $m->year)
//                 ->whereMonth('payment_date', $m->month)
//                 ->sum('amount');

//             $trendRevenue[] = $mRevJamaah + $mRevLayanan;
//             $trendExpense[] = $mTrip + $mOp + $mVendor;
//         }

//         return view('keuangan.laporan.index', [
//             'periode' => $periode,

//             // REVENUE
//             'totalRevenueJamaah' => $revJamaah,
//             'totalServiceRevenue' => $revLayanan,
//             'totalRevenueAll' => $totalRevenue,

//             // EXPENSES
//             'totalTripExpenses' => $expTrip,
//             'totalOperational'  => $expOp,
//             'totalVendorExpense'=> $expVendor,
//             'totalExpenseAll'   => $totalExpense,

//             // NET PROFIT
//             'netAll' => $netAll,

//             // TREND
//             'months' => $months,
//             'trendRevenue' => $trendRevenue,
//             'trendExpense' => $trendExpense,
//         ]);
//     }

//     /* ==========================================================
//     PNL BULANAN — FINAL F4 PREMIUM
//     ========================================================== */
//     public function monthlyPnl(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $fromCarbon = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
//         $toCarbon   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

//         $from = $fromCarbon->format('Y-m-d');
//         $to   = $toCarbon->format('Y-m-d');

//         $bulanNama = $fromCarbon->translatedFormat('F');

//         /* ============================
//         PENDAPATAN
//         ============================ */
//         $revenueJamaah = Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from, $to])
//             ->sum('jumlah');

//         $revenueLayanan = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from, $to])
//             ->sum('amount');

//         $totalRevenue = $revenueJamaah + $revenueLayanan;

//         /* ============================
//         HPP
//         ============================ */
//         $tripExpenses = TripExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         $vendorExpenses = VendorPayment::whereBetween('payment_date', [$from,$to])
//             ->sum('amount');

//         $hpp = $tripExpenses + $vendorExpenses;

//         /* ============================
//         OPERASIONAL
//         ============================ */
//         $operational = OperationalExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         /* ============================
//         PROFIT
//         ============================ */
//         $grossProfit = $totalRevenue - $hpp;
//         $netProfit   = $grossProfit - $operational;

//         return view('keuangan.laporan.pnl', compact(
//             'bulan', 'tahun', 'bulanNama',
//             'from','to',
//             'revenueJamaah', 'revenueLayanan', 'totalRevenue',
//             'tripExpenses', 'vendorExpenses', 'hpp',
//             'operational',
//             'grossProfit', 'netProfit'
//         ));
//     }

//     /**
//      * TRIP PROFIT - WEB (LIST)
//      */
//     public function tripProfit(Request $request)
//     {
//         $paketList = PaketMaster::orderBy('nama_paket')->get();
//         $paketId = $request->get('paket_id');

//         $query = Keberangkatan::with('paket')->orderBy('tanggal_berangkat','desc');
//         if ($paketId) $query->where('id_paket_master', $paketId);
//         $keberangkatan = $query->paginate(20)->appends($request->query());

//         $items = $keberangkatan->getCollection()->map(function ($k) {

//             // REVENUE from payments + jamaah
//             $revenue = DB::table('payments')
//                 ->join('jamaah','payments.jamaah_id','=','jamaah.id')
//                 ->where('jamaah.id_keberangkatan', $k->id)
//                 ->where('payments.status','valid')
//                 ->sum('payments.jumlah');

//             // TRIP COST based on paket_id only
//             $tripCost = TripExpenses::where('paket_id', $k->id_paket_master)
//                 ->sum('jumlah');

//             return (object)[
//                 'keberangkatan_id' => $k->id,
//                 'kode'      => $k->kode_keberangkatan ?? '-',
//                 'paket'     => $k->paket?->nama_paket ?? '-',
//                 'tanggal'   => $k->tanggal_berangkat,
//                 'revenue'   => (int)$revenue,
//                 'trip_cost' => (int)$tripCost,
//                 'profit'    => (int)($revenue - $tripCost),
//             ];
//         });

//         $keberangkatan->setCollection($items);

//         return view('keuangan.laporan.trip_profit', compact(
//             'keberangkatan','paketList','paketId'
//         ));
//     }

//     /**
//      * TRIP PROFIT - PDF F4 (EXPORT)
//      */
//     public function tripProfitPdf(Request $request)
//     {
//         $paketId = $request->get('paket_id');

//         // 1. Ambil daftar keberangkatan + paket_id
//         $query = Keberangkatan::select(
//             'keberangkatan.id',
//             'keberangkatan.id_paket_master',
//             'keberangkatan.kode_keberangkatan',
//             'keberangkatan.tanggal_berangkat',
//             'paket_master.nama_paket as paket'
//         )
//         ->join('paket_master', 'paket_master.id', '=', 'keberangkatan.id_paket_master')
//         ->orderBy('keberangkatan.tanggal_berangkat', 'desc');

//         if ($paketId) {
//             $query->where('keberangkatan.id_paket_master', $paketId);
//         }

//         $keberangkatan = $query->get();

//         // 2. Hitung revenue + trip cost berdasarkan paket_id (BENAR)
//         $keberangkatan = $keberangkatan->map(function ($row) {

//             // Revenue
//             $revenue = DB::table('payments')
//                 ->join('jamaah', 'jamaah.id', '=', 'payments.jamaah_id')
//                 ->where('jamaah.id_keberangkatan', $row->id)
//                 ->where('payments.status', 'valid')
//                 ->sum('payments.jumlah');

//             // Trip Cost berdasarkan paket_id
//             $tripCost = DB::table('trip_expenses')
//                 ->where('paket_id', $row->id_paket_master)   // <-- FIX
//                 ->sum('jumlah');

//             $row->revenue   = (int)$revenue;
//             $row->trip_cost = (int)$tripCost;
//             $row->profit    = (int)($revenue - $tripCost);

//             return $row;
//         });

//         // 3. Generate PDF
//         $pdf = PDF::loadView('keuangan.laporan.pdf.trip_profit-f4', [
//             'keberangkatan' => $keberangkatan
//         ])->setPaper('F4', 'portrait');

//         return $pdf->stream('Trip-Profit.pdf');
//     }

//     /**
//      * CASHFLOW UI + data untuk export/chart
//      */
//     public function cashflow(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->toDateString();
//         $to   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();
//         $bulanNama = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');

//         // ----------------------------
//         // CASH IN
//         //  - Pembayaran Jamaah (payments.status = valid)
//         //  - Pendapatan Layanan (layanan_payments.status = approved)
//         //  grouped by date
//         // ----------------------------
//         $cashInJamaah = DB::table('payments')
//             ->select(DB::raw("DATE(tanggal_bayar) as tanggal"), DB::raw("SUM(jumlah) as total_in"))
//             ->where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from, $to])
//             ->groupBy('tanggal')
//             ->orderBy('tanggal')
//             ->get()
//             ->map(function($r){
//                 return (object)[
//                     'tanggal' => $r->tanggal,
//                     'total_in' => (float)$r->total_in,
//                     'sumber' => 'Pembayaran Jamaah'
//                 ];
//             });

//         $cashInLayanan = DB::table('layanan_payments')
//             ->select(DB::raw("DATE(created_at) as tanggal"), DB::raw("SUM(amount) as total_in"))
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from, $to])
//             ->groupBy('tanggal')
//             ->orderBy('tanggal')
//             ->get()
//             ->map(function($r){
//                 return (object)[
//                     'tanggal' => $r->tanggal,
//                     'total_in' => (float)$r->total_in,
//                     'sumber' => 'Pendapatan Layanan'
//                 ];
//             });

//         // merge collections and sort by date
//         $cashIn = $cashInJamaah->merge($cashInLayanan)
//                     ->sortBy('tanggal')
//                     ->values();

//         $totalCashIn = $cashIn->sum('total_in');

//         // ----------------------------
//         // CASH OUT
//         //  - trip_expenses (tanggal)
//         //  - operational_expenses (tanggal)
//         //  - vendor_payments (payment_date)
//         // ----------------------------
//         $tripOut = DB::table('trip_expenses')
//             ->select(DB::raw('tanggal'), DB::raw('"Biaya Keberangkatan" as kategori'), DB::raw('jumlah'))
//             ->whereBetween('tanggal', [$from, $to]);

//         $operOut = DB::table('operational_expenses')
//             ->select(DB::raw('tanggal'), DB::raw('"Operasional" as kategori'), DB::raw('jumlah'))
//             ->whereBetween('tanggal', [$from, $to]);

//         $vendorOut = DB::table('vendor_payments')
//             ->select(DB::raw('payment_date as tanggal'), DB::raw('"Vendor" as kategori'), DB::raw('amount as jumlah'))
//             ->whereBetween('payment_date', [$from, $to]);

//         // union semua -> get results (as collection of stdClass)
//         $cashOut = $tripOut->unionAll($operOut)->unionAll($vendorOut)->orderBy('tanggal')->get();

//         // normalisasi field nama agar blade konsisten
//         $cashOut = $cashOut->map(function($r){
//             return (object)[
//                 'tanggal' => $r->tanggal,
//                 'kategori' => $r->kategori,
//                 'jumlah' => (float) ($r->jumlah ?? $r->amount ?? 0)
//             ];
//         });

//         $totalCashOut = $cashOut->sum('jumlah');

//         $netCashflow = $totalCashIn - $totalCashOut;

//         // ----------------------------
//         // TREND 6 BULAN
//         // ----------------------------
//         $months = [];
//         $trendIn = [];
//         $trendOut = [];

//         for ($i = 5; $i >= 0; $i--) {
//             $m = Carbon::now()->subMonths($i);
//             $months[] = $m->format('M Y');

//             $inJamaah = DB::table('payments')
//                 ->where('status','valid')
//                 ->whereYear('tanggal_bayar', $m->year)
//                 ->whereMonth('tanggal_bayar', $m->month)
//                 ->sum('jumlah');

//             $inLayanan = DB::table('layanan_payments')
//                 ->where('status','approved')
//                 ->whereYear('created_at', $m->year)
//                 ->whereMonth('created_at', $m->month)
//                 ->sum('amount');

//             $outTrip = DB::table('trip_expenses')
//                 ->whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah');

//             $outOper = DB::table('operational_expenses')
//                 ->whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah');

//             $outVendor = DB::table('vendor_payments')
//                 ->whereYear('payment_date', $m->year)
//                 ->whereMonth('payment_date', $m->month)
//                 ->sum('amount');

//             $trendIn[] = $inJamaah + $inLayanan;
//             $trendOut[] = $outTrip + $outOper + $outVendor;
//         }

//         // return view with all variables blade expects
//         return view('keuangan.laporan.cashflow', compact(
//             'bulan', 'tahun', 'bulanNama',
//             'from', 'to',
//             'cashIn', 'cashOut',
//             'totalCashIn', 'totalCashOut', 'netCashflow',
//             'months', 'trendIn', 'trendOut'
//         ));
//     }


//     /* ==============================
//     EXPORT PDF (F4) - Cashflow
//     ============================== */
//     public function exportCashflowPdf(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
//         $to   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
//         $fromCarbon = Carbon::createFromDate($tahun, $bulan, 1);
//         $bulanNama = $fromCarbon->translatedFormat('F');

//         // compute same as above (copy same queries)
//         $cashInJamaah = \App\Models\Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from, $to])->sum('jumlah');

//         $cashInLayanan = \DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from, $to])->sum('amount');

//         $totalCashIn = $cashInJamaah + $cashInLayanan;

//         $tripExpenses = \App\Models\TripExpenses::whereBetween('tanggal', [$from, $to])->sum('jumlah');
//         $vendorPayments = \App\Models\VendorPayment::whereBetween('payment_date', [$from, $to])->sum('amount');
//         $operational = \App\Models\OperationalExpenses::whereBetween('tanggal', [$from, $to])->sum('jumlah');

//         $totalCashOut = $tripExpenses + $vendorPayments + $operational;
//         $netCashflow = $totalCashIn - $totalCashOut;

//         $pdf = Pdf::loadView('keuangan.laporan.pdf.cashflow-f4', compact(
//             'bulan','tahun','bulanNama','from','to',
//             'cashInJamaah','cashInLayanan','totalCashIn',
//             'tripExpenses','vendorPayments','operational','totalCashOut','netCashflow'
//         ));

//         try { $pdf->setPaper('F4','portrait'); } catch (\Exception $e) { $pdf->setPaper('A4','portrait'); }
//         return $pdf->stream("cashflow-{$bulan}-{$tahun}.pdf");
//     }

//     /* ==============================
//     EXPORT EXCEL - Cashflow
//     ============================== */
//     public function exportCashflowExcel(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
//         $to   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

//         // compute sums
//         $cashInJamaah = \App\Models\Payments::where('status','valid')->whereBetween('tanggal_bayar', [$from, $to])->sum('jumlah');
//         $cashInLayanan = \DB::table('layanan_payments')->where('status','approved')->whereBetween('created_at', [$from,$to])->sum('amount');
//         $totalCashIn = $cashInJamaah + $cashInLayanan;
//         $tripExpenses = \App\Models\TripExpenses::whereBetween('tanggal', [$from,$to])->sum('jumlah');
//         $vendorPayments = \App\Models\VendorPayment::whereBetween('payment_date', [$from,$to])->sum('amount');
//         $operational = \App\Models\OperationalExpenses::whereBetween('tanggal', [$from,$to])->sum('jumlah');
//         $totalCashOut = $tripExpenses + $vendorPayments + $operational;
//         $netCashflow = $totalCashIn - $totalCashOut;

//         return Excel::download(new CashflowExport(
//             $from, $to,
//             compact('cashInJamaah','cashInLayanan','totalCashIn','tripExpenses','vendorPayments','operational','totalCashOut','netCashflow')
//         ), "cashflow-{$from}-{$to}.xlsx");
//     }

//    /* ==========================================================
//    EXPORT EXCEL — FIXED (Sync dengan Dashboard)
//     ========================================================== */
//     public function exportExcel(Request $request)
//     {
//         $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
//         $to   = $request->get('to', now()->endOfMonth()->format('Y-m-d'));

//         // ===============================
//         // REVENUE
//         // ===============================
//         $revenueJamaah = Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from,$to])
//             ->sum('jumlah');

//         $revenueLayanan = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from,$to])
//             ->sum('amount');

//         $revenues = $revenueJamaah + $revenueLayanan;

//         // ===============================
//         // HPP
//         // ===============================
//         $tripExpenses = TripExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         $vendor = VendorPayment::whereBetween('payment_date', [$from,$to])
//             ->sum('amount');

//         // ===============================
//         // OPERASIONAL
//         // ===============================
//         $operational = OperationalExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         // ===============================
//         // EXPORT EXCEL
//         // ===============================
//         return Excel::download(new LaporanPnlExport(
//             $from,
//             $to,
//             $revenues,
//             $tripExpenses,
//             $operational,
//             $vendor
//         ), "PNL-$from-$to.xlsx");
//     }


//     /* ==========================================================
//     CASHFLOW PDF FINAL
//     ========================================================== */
//     public function cashflowPdf(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
//         $to   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

//         $bulanNama = $from->translatedFormat('F');

//         /* ==========================================================
//         CASH IN — PEMBAYARAN JEMAAH
//         ========================================================== */
//         $cashInJamaahList = Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from, $to])
//             ->select(
//                 DB::raw("tanggal_bayar as tanggal"),
//                 DB::raw("'Pembayaran Jamaah' as sumber"),
//                 DB::raw("jumlah as total_in")
//             )
//             ->get();

//         $cashInJamaah = $cashInJamaahList->sum('total_in');

//         /* ==========================================================
//         CASH IN — PEMBAYARAN LAYANAN (B2B)
//         ========================================================== */
//         $cashInLayananList = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from, $to])
//             ->select(
//                 DB::raw("DATE(created_at) as tanggal"),
//                 DB::raw("'Pembayaran Layanan (B2B)' as sumber"),
//                 DB::raw("amount as total_in")
//             )
//             ->get();

//         $cashInLayanan = $cashInLayananList->sum('total_in');

//         /* ==========================================================
//         GABUNGKAN CASH-IN (concat → BUKAN merge)
//         ========================================================== */
//         $cashIn = $cashInJamaahList
//             ->concat($cashInLayananList)
//             ->sortBy('tanggal')
//             ->values();

//         $totalCashIn = $cashInJamaah + $cashInLayanan;



//         /* ==========================================================
//         CASH OUT — TRIP EXPENSES
//         ========================================================== */
//         $tripList = TripExpenses::whereBetween('tanggal', [$from, $to])
//             ->select(
//                 DB::raw("tanggal"),
//                 DB::raw("'Biaya Trip' as kategori"),
//                 DB::raw("jumlah as jumlah")
//             )
//             ->get();

//         $tripExpenses = $tripList->sum('jumlah');

//         /* ==========================================================
//         CASH OUT — VENDOR PAYMENTS
//         ========================================================== */
//         $vendorList = VendorPayment::whereBetween('payment_date', [$from, $to])
//             ->select(
//                 DB::raw("payment_date as tanggal"),
//                 DB::raw("'Vendor Payment (B2B)' as kategori"),
//                 DB::raw("amount as jumlah")
//             )
//             ->get();

//         $vendorPayments = $vendorList->sum('jumlah');

//         /* ==========================================================
//         CASH OUT — OPERASIONAL
//         ========================================================== */
//         $operList = OperationalExpenses::whereBetween('tanggal', [$from, $to])
//             ->select(
//                 DB::raw("tanggal"),
//                 DB::raw("'Operasional' as kategori"),
//                 DB::raw("jumlah as jumlah")
//             )
//             ->get();

//         $operational = $operList->sum('jumlah');

//         /* ==========================================================
//         GABUNGKAN CASH-OUT (concat → aman untuk stdClass/Eloquent)
//         ========================================================== */
//         $cashOut = $tripList
//             ->concat($vendorList)
//             ->concat($operList)
//             ->sortBy('tanggal')
//             ->values();

//         $totalCashOut = $tripExpenses + $vendorPayments + $operational;

//         /* ==========================================================
//         NET CASHFLOW
//         ========================================================== */
//         $netCashflow = $totalCashIn - $totalCashOut;



//         /* ==========================================================
//         GENERATE PDF
//         ========================================================== */
//         $pdf = PDF::loadView('keuangan.laporan.pdf.cashflow-f4', [
//             'bulan'          => $bulan,
//             'tahun'          => $tahun,
//             'bulanNama'      => $bulanNama,

//             'cashInJamaah'   => $cashInJamaah,
//             'cashInLayanan'  => $cashInLayanan,
//             'totalCashIn'    => $totalCashIn,

//             'tripExpenses'   => $tripExpenses,
//             'vendorPayments' => $vendorPayments,
//             'operational'    => $operational,
//             'totalCashOut'   => $totalCashOut,

//             'netCashflow'    => $netCashflow,

//             'cashIn'         => $cashIn,
//             'cashOut'        => $cashOut
//         ])->setPaper('F4', 'portrait');

//         return $pdf->stream("Cashflow-{$bulanNama}-{$tahun}.pdf");
//     }

//     /* ==========================================================
//        EXPORT PDF F4 PREMIUM
//     ========================================================== */
//    public function exportPdf(Request $request)
//     {
//         $bulan = $request->get('bulan', now()->format('m'));
//         $tahun = $request->get('tahun', now()->format('Y'));

//         $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
//         $to   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

//         $bulanNama = $from->translatedFormat('F');

//         // ============================
//         // PENDAPATAN
//         // ============================
//         $revenueJamaah = Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from, $to])
//             ->sum('jumlah');

//         $revenueLayanan = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from, $to])
//             ->sum('amount');

//         $totalRevenue = $revenueJamaah + $revenueLayanan;

//         // ============================
//         // HPP
//         // ============================
//         $tripExpenses = TripExpenses::whereBetween('tanggal', [$from,$to])->sum('jumlah');
//         $vendorExpenses = VendorPayment::whereBetween('payment_date', [$from,$to])->sum('amount');
//         $hpp = $tripExpenses + $vendorExpenses;

//         // ============================
//         // OPERASIONAL
//         // ============================
//         $operational = OperationalExpenses::whereBetween('tanggal', [$from,$to])->sum('jumlah');

//         // ============================
//         // PROFIT
//         // ============================
//         $grossProfit = $totalRevenue - $hpp;
//         $netProfit   = $grossProfit - $operational;

//         // ============================
//         // EXPORT PDF
//         // ============================
//         $pdf = Pdf::loadView('keuangan.laporan.pdf.pnl-f4', compact(
//             'bulan', 'tahun', 'bulanNama',
//             'from','to',
//             'revenueJamaah','revenueLayanan','totalRevenue',
//             'tripExpenses','vendorExpenses','hpp',
//             'operational','grossProfit','netProfit'
//         ));

//         try {
//             $pdf->setPaper('F4','portrait');
//         } catch (\Exception $e) {
//             $pdf->setPaper('A4','portrait');
//         }

//         return $pdf->stream("PNL-{$bulan}-{$tahun}.pdf");
//     }

//     public function pnlExcel(Request $request)
//     {
//         return $this->exportExcel($request);
//     }

//     public function pnlPdf(Request $request)
//     {
//         return $this->exportPdf($request);
//     }

// }
