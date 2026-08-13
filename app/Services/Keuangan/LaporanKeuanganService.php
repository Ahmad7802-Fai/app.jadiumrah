<?php

namespace App\Services\Keuangan;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// MODELS
use App\Models\Payments;
use App\Models\TripExpenses;
use App\Models\OperationalExpenses;
use App\Models\VendorPayment;
use App\Models\MarketingExpenses;

class LaporanKeuanganService
{
    /* =====================================================
     | PARSER PERIODE (YYYY-MM)
     ===================================================== */
    protected function parsePeriode(string $periode): array
    {
        return [
            'year'  => (int) substr($periode, 0, 4),
            'month' => (int) substr($periode, 5, 2),
        ];
    }

    /* =====================================================
     | REVENUE BULANAN
     ===================================================== */
    protected function revenue(int $year, int $month): array
    {
        $jamaah = Payments::where('status', 'valid')
            ->whereYear('tanggal_bayar', $year)
            ->whereMonth('tanggal_bayar', $month)
            ->sum('jumlah');

        $layanan = DB::table('layanan_payments')
            ->where('status', 'approved')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('amount');

        return [
            'jamaah'  => (int) $jamaah,
            'layanan' => (int) $layanan,
            'total'   => (int) ($jamaah + $layanan),
        ];
    }

    /* =====================================================
     | EXPENSE BULANAN (FIX MARKETING)
     ===================================================== */
    protected function expense(int $year, int $month): array
    {
        $trip = TripExpenses::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('jumlah');

        $operational = OperationalExpenses::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('jumlah');

        $vendor = VendorPayment::whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount');

        // 🔥 FIX UTAMA
        $marketing = MarketingExpenses::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->sum('biaya');

        return [
            'trip'        => (int) $trip,
            'operational' => (int) $operational,
            'vendor'      => (int) $vendor,
            'marketing'   => (int) $marketing,
            'total'       => (int) ($trip + $operational + $vendor + $marketing),
        ];
    }

    /* =====================================================
     | SUMMARY BULANAN (DASHBOARD CARD)
     ===================================================== */
    public function summaryBulanan(string $periode): array
    {
        ['year' => $year, 'month' => $month] = $this->parsePeriode($periode);

        $revenue = $this->revenue($year, $month);
        $expense = $this->expense($year, $month);

        return [
            'pendapatan_jamaah'  => $revenue['jamaah'],
            'pendapatan_layanan' => $revenue['layanan'],
            'total_pendapatan'   => $revenue['total'],

            'biaya_trip'         => $expense['trip'],
            'biaya_operasional'  => $expense['operational'],
            'biaya_vendor'       => $expense['vendor'],
            'biaya_marketing'    => $expense['marketing'],
            'total_pengeluaran'  => $expense['total'],

            'laba_bersih'        => $revenue['total'] - $expense['total'],
        ];
    }
    /* =====================================================
     | PNL BULANAN
     ===================================================== */
    public function pnlBulanan(int $bulan, int $tahun): array
    {
        $revenue = $this->revenue($tahun, $bulan);
        $expense = $this->expense($tahun, $bulan);

        $hpp  = $expense['trip'] + $expense['vendor'];
        $opex = $expense['operational'] + $expense['marketing'];

        $grossProfit = $revenue['total'] - $hpp;
        $netProfit   = $grossProfit - $opex;

        return [
            // ================= REVENUE =================
            'revenue_jamaah'   => $revenue['jamaah'],
            'revenue_layanan'  => $revenue['layanan'],
            'total_revenue'    => $revenue['total'],

            // ================= HPP =================
            'biaya_trip'       => $expense['trip'],
            'biaya_vendor'     => $expense['vendor'],
            'total_hpp'        => $hpp,

            // ================= OPEX =================
            'biaya_operasional'=> $expense['operational'],
            'biaya_marketing'  => $expense['marketing'],
            'total_opex'       => $opex,

            // ================= PROFIT =================
            'gross_profit'     => $grossProfit,
            'net_profit'       => $netProfit,
        ];
    }


    /* =====================================================
     | CASHFLOW BULANAN
     ===================================================== */
    public function cashflowBulanan(int $bulan, int $tahun): array
    {
        $from = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $to   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // CASH IN
        $cashInJamaah = Payments::where('status','valid')
            ->whereBetween('tanggal_bayar', [$from, $to])
            ->sum('jumlah');

        $cashInLayanan = DB::table('layanan_payments')
            ->where('status','approved')
            ->whereBetween('created_at', [$from, $to])
            ->sum('amount');

        $totalCashIn = $cashInJamaah + $cashInLayanan;

        // CASH OUT
        $tripOut = TripExpenses::whereBetween('tanggal', [$from, $to])
            ->sum('jumlah');

        $operOut = OperationalExpenses::whereBetween('tanggal', [$from, $to])
            ->sum('jumlah');

        $vendorOut = VendorPayment::whereBetween('payment_date', [$from, $to])
            ->sum('amount');

        $marketingOut = MarketingExpenses::whereBetween('tanggal', [$from, $to])
            ->sum('biaya');

        $totalCashOut = $tripOut + $operOut + $vendorOut + $marketingOut;

        return [
            'cashIn'  => [
                'jamaah'  => (int) $cashInJamaah,
                'layanan' => (int) $cashInLayanan,
                'total'   => (int) $totalCashIn,
            ],
            'cashOut' => [
                'trip'        => (int) $tripOut,
                'operational' => (int) $operOut,
                'vendor'      => (int) $vendorOut,
                'marketing'   => (int) $marketingOut,
                'total'       => (int) $totalCashOut,
            ],
            'netCashflow' => (int) ($totalCashIn - $totalCashOut),
        ];
    }

    /* =====================================================
     | TREND 6 BULAN (CHART)
     ===================================================== */
    public function trend6Bulan(): array
    {
        $months = [];
        $revenueTrend = [];
        $expenseTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);

            $months[] = $m->format('M Y');

            $rev = $this->revenue($m->year, $m->month);
            $exp = $this->expense($m->year, $m->month);

            $revenueTrend[] = $rev['total'];
            $expenseTrend[] = $exp['total'];
        }

        return compact('months', 'revenueTrend', 'expenseTrend');
    }

    public function trendCashflow6Bulan(): array
    {
        $months = [];
        $cashInTrend = [];
        $cashOutTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);

            $months[] = $m->format('M Y');

            $cashflow = $this->cashflowBulanan($m->month, $m->year);

            $cashInTrend[]  = $cashflow['cashIn']['total'];
            $cashOutTrend[] = $cashflow['cashOut']['total'];
        }

        return [
            'months'  => $months,
            'cashIn'  => $cashInTrend,
            'cashOut' => $cashOutTrend,
        ];
    }

}

// namespace App\Services\Keuangan;

// use Illuminate\Support\Facades\DB;
// use Carbon\Carbon;

// // MODELS
// use App\Models\Payments;
// use App\Models\TripExpenses;
// use App\Models\OperationalExpenses;
// use App\Models\VendorPayment;
// use App\Models\MarketingExpenses;

// class LaporanKeuanganService
// {
//     /**
//      * ==========================
//      * SUMMARY BULANAN
//      * ==========================
//      */
//     public function summaryBulanan(string $periode): array
//     {
//         $year  = (int) substr($periode, 0, 4);
//         $month = (int) substr($periode, 5, 2);

//         // =======================
//         // REVENUE
//         // =======================
//         $revJamaah = Payments::where('status','valid')
//             ->whereYear('tanggal_bayar', $year)
//             ->whereMonth('tanggal_bayar', $month)
//             ->sum('jumlah');

//         $revLayanan = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereYear('created_at', $year)
//             ->whereMonth('created_at', $month)
//             ->sum('amount');

//         // =======================
//         // EXPENSES
//         // =======================
//         $expTrip = TripExpenses::whereYear('tanggal', $year)
//             ->whereMonth('tanggal', $month)
//             ->sum('jumlah');

//         $expOperational = OperationalExpenses::whereYear('tanggal', $year)
//             ->whereMonth('tanggal', $month)
//             ->sum('jumlah');

//         $expVendor = VendorPayment::whereYear('payment_date', $year)
//             ->whereMonth('payment_date', $month)
//             ->sum('amount');

//         $expMarketing = MarketingExpenses::whereYear('tanggal', $year)
//             ->whereMonth('tanggal', $month)
//             ->sum('biaya');

//         // =======================
//         // TOTAL
//         // =======================
//         $totalRevenue = $revJamaah + $revLayanan;
//         $totalExpense = $expTrip + $expOperational + $expVendor + $expMarketing;
//         $netProfit    = $totalRevenue - $totalExpense;

//         return [
//             'totalRevenue' => $totalRevenue,
//             'totalExpense' => $totalExpense,
//             'netProfit'    => $netProfit,

//             // DETAIL dipakai UI / PDF / API
//             'detail' => [
//                 'revJamaah'     => $revJamaah,
//                 'revLayanan'    => $revLayanan,
//                 'expTrip'       => $expTrip,
//                 'expOperational'=> $expOperational,
//                 'expVendor'     => $expVendor,
//                 'expMarketing'  => $expMarketing,
//             ],
//         ];
//     }


//     /* =====================================================
//      | PNL BULANAN
//      ===================================================== */
//     public function pnlBulanan(int $bulan, int $tahun): array
// {
//     $revJamaah = Payments::where('status','valid')
//         ->whereYear('tanggal_bayar', $tahun)
//         ->whereMonth('tanggal_bayar', $bulan)
//         ->sum('jumlah');

//     $revLayanan = DB::table('layanan_payments')
//         ->where('status','approved')
//         ->whereYear('created_at', $tahun)
//         ->whereMonth('created_at', $bulan)
//         ->sum('amount');

//     $trip = TripExpenses::whereYear('tanggal', $tahun)
//         ->whereMonth('tanggal', $bulan)
//         ->sum('jumlah');

//     $operational = OperationalExpenses::whereYear('tanggal', $tahun)
//         ->whereMonth('tanggal', $bulan)
//         ->sum('jumlah');

//     $vendor = VendorPayment::whereYear('payment_date', $tahun)
//         ->whereMonth('payment_date', $bulan)
//         ->sum('amount');

//     return [
//         // 🔥 NAMA LAMA (BIAR BLADE AMAN)
//         'totalRevenueJamaah'  => $revJamaah,
//         'totalServiceRevenue' => $revLayanan,
//         'totalTripExpenses'   => $trip,
//         'totalOperational'    => $operational,
//         'totalVendorExpense'  => $vendor,

//         'totalRevenueAll' => $revJamaah + $revLayanan,
//         'totalExpenseAll' => $trip + $operational + $vendor,
//         'netAll'          => ($revJamaah + $revLayanan) - ($trip + $operational + $vendor),
//     ];
// }

//     /* =====================================================
//      | CASHFLOW BULANAN
//      ===================================================== */
//     public function cashflowBulanan(int $bulan, int $tahun): array
//     {
//         $from = Carbon::create($tahun, $bulan, 1)->startOfMonth();
//         $to   = Carbon::create($tahun, $bulan, 1)->endOfMonth();

//         // ================= CASH IN =================
//         $cashInJamaah = Payments::where('status','valid')
//             ->whereBetween('tanggal_bayar', [$from,$to])
//             ->sum('jumlah');

//         $cashInLayanan = DB::table('layanan_payments')
//             ->where('status','approved')
//             ->whereBetween('created_at', [$from,$to])
//             ->sum('amount');

//         $totalCashIn = $cashInJamaah + $cashInLayanan;

//         // ================= CASH OUT =================
//         $tripOut = TripExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         $operOut = OperationalExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('jumlah');

//         $vendorOut = VendorPayment::whereBetween('payment_date', [$from,$to])
//             ->sum('amount');

//         $marketingOut = MarketingExpenses::whereBetween('tanggal', [$from,$to])
//             ->sum('biaya');

//         $totalCashOut = $tripOut + $operOut + $vendorOut + $marketingOut;
//         $netCashflow  = $totalCashIn - $totalCashOut;

//         return compact(
//             'from','to',
//             'cashInJamaah','cashInLayanan','totalCashIn',
//             'tripOut','operOut','vendorOut','marketingOut',
//             'totalCashOut','netCashflow'
//         );
//     }

//     /* =====================================================
//      | TREND 6 BULAN (UNTUK CHART)
//      ===================================================== */
//     public function trend6Bulan(): array
// {
//     $months = [];
//     $trendRevenue = [];
//     $trendExpense = [];

//     for ($i = 5; $i >= 0; $i--) {
//         $m = Carbon::now()->subMonths($i);

//         $months[] = $m->format('M Y');

//         $revenue =
//             Payments::where('status','valid')
//                 ->whereYear('tanggal_bayar', $m->year)
//                 ->whereMonth('tanggal_bayar', $m->month)
//                 ->sum('jumlah')
//             +
//             DB::table('layanan_payments')
//                 ->where('status','approved')
//                 ->whereYear('created_at', $m->year)
//                 ->whereMonth('created_at', $m->month)
//                 ->sum('amount');

//         $expense =
//             TripExpenses::whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah')
//             +
//             OperationalExpenses::whereYear('tanggal', $m->year)
//                 ->whereMonth('tanggal', $m->month)
//                 ->sum('jumlah')
//             +
//             VendorPayment::whereYear('payment_date', $m->year)
//                 ->whereMonth('payment_date', $m->month)
//                 ->sum('amount');

//         $trendRevenue[] = $revenue;
//         $trendExpense[] = $expense;
//     }

//     return compact('months', 'trendRevenue', 'trendExpense');
// }

// }
