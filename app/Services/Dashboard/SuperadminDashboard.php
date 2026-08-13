<?php

namespace App\Services\Dashboard;

use App\Models\{
    Payments,
    LayananPayment,
    TripExpense,
    OperationalExpense,
    MarketingExpenses,
    Invoices,
    LayananInvoice,
    TabunganUmrah,
    Agent,
    Branch,
    Jamaah
};

class SuperadminDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'dashboard.superadmin';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard Superadmin';
    }

    /* ===============================
       CARDS (REWRITE DARI VERSI LAMA)
       - Periodik (bulan & tahun)
       - Master (all time)
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        return [

            /* ===============================
               📊 PERIODIK (FILTERED)
            =============================== */

            $this->card(
                'Omzet Jamaah',
                Payments::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('jumlah'),
                'fa-wallet'
            ),

            $this->card(
                'Omzet Layanan',
                LayananPayment::where('status', 'approved')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('amount'),
                'fa-briefcase'
            ),

            $this->card(
                'Pengeluaran Jamaah',
                TripExpense::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('jumlah'),
                'fa-plane-departure'
            ),

            $this->card(
                'Operasional Kantor',
                OperationalExpense::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('jumlah'),
                'fa-building'
            ),

            $this->card(
                'Marketing Spend',
                MarketingExpenses::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('biaya'),
                'fa-bullhorn'
            ),

            $this->card(
                'Tagihan Jamaah',
                Invoices::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('sisa_tagihan'),
                'fa-file-invoice'
            ),

            $this->card(
                'Tagihan Layanan',
                LayananInvoice::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('amount'),
                'fa-file-contract'
            ),

            $this->card(
                'Dana Tabungan',
                TabunganUmrah::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('saldo'),
                'fa-piggy-bank'
            ),

            $this->card(
                'Jamaah Menabung',
                TabunganUmrah::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->distinct('jamaah_id')
                    ->count('jamaah_id'),
                'fa-user-check'
            ),

            /* ===============================
               🧩 MASTER (ALL TIME)
            =============================== */

            $this->card(
                'Total Agen',
                Agent::count(),
                'fa-user-tie'
            ),

            $this->card(
                'Total Cabang',
                Branch::count(),
                'fa-sitemap'
            ),

            $this->card(
                'Total Jamaah',
                Jamaah::count(),
                'fa-users'
            ),
        ];
    }

    /* ===============================
       CHART UTAMA
       (Omzet vs Pengeluaran)
    =============================== */
    protected function chartLabels(): array
    {
        return ['Omzet', 'Pengeluaran'];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        $omzet =
            Payments::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('jumlah')
          + LayananPayment::where('status', 'approved')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('amount');

        $pengeluaran =
            TripExpense::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('jumlah')
          + OperationalExpense::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('jumlah')
          + MarketingExpenses::whereMonth('tanggal', $month)->whereYear('tanggal', $year)->sum('biaya');

        return [$omzet, $pengeluaran];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        $last = now()->subMonth();

        $omzet =
            Payments::whereMonth('created_at', $last->month)->whereYear('created_at', $last->year)->sum('jumlah')
          + LayananPayment::where('status', 'approved')
                ->whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->sum('amount');

        $pengeluaran =
            TripExpense::whereMonth('tanggal', $last->month)->whereYear('tanggal', $last->year)->sum('jumlah')
          + OperationalExpense::whereMonth('tanggal', $last->month)->whereYear('tanggal', $last->year)->sum('jumlah')
          + MarketingExpenses::whereMonth('tanggal', $last->month)->whereYear('tanggal', $last->year)->sum('biaya');

        return [$omzet, $pengeluaran];
    }

    /* ===============================
       CHART COMPARISON
       (OMZET SAJA)
    =============================== */
    protected function chartComparisonThisMonth(): array
    {
        return [
            Payments::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah')
          + LayananPayment::where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];
    }

    protected function chartComparisonLastMonth(): array
    {
        $last = now()->subMonth();

        return [
            Payments::whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->sum('jumlah')
          + LayananPayment::where('status', 'approved')
                ->whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->sum('amount'),
        ];
    }
}


// namespace App\Services\Dashboard;

// use App\Models\{
//     Payments,
//     LayananPayment,
//     TripExpense,
//     OperationalExpense,
//     Invoices,
//     LayananInvoice,
//     TabunganUmrah,
//     Agent,
//     Branch,
//     Jamaah,
//     MarketingExpenses
// };

// class SuperadminDashboard extends AbstractDashboard
// {
//     /* ===============================
//        TITLE
//     =============================== */
//     protected function title(): string
//     {
//         return 'Dashboard Superadmin';
//     }

//     /* ===============================
//        CARDS
//     =============================== */
//     protected function buildCards(int $month, int $year): array
//     {
//         return [
//             $this->card(
//                 'Pembayaran Masuk',
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),
//                 'money'
//             ),

//             $this->card(
//                 'Total Jamaah',
//                 Jamaah::count(),
//                 'users'
//             ),

//             $this->card(
//                 'Total Agent',
//                 Agent::count(),
//                 'user-tie'
//             ),

//             $this->card(
//                 'Total Cabang',
//                 Branch::count(),
//                 'building'
//             ),
//         ];
//     }

//     /* ===============================
//        CHART UTAMA
//     =============================== */
//     protected function buildChart(int $month, int $year): array
//     {
//         return [
//             'labels' => ['Total'],
//             'thisMonth' => [
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),
//             ],
//             'lastMonth' => [
//                 Payments::whereMonth('created_at', now()->subMonth()->month)
//                     ->whereYear('created_at', now()->subMonth()->year)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     /* ===============================
//        CHART COMPARISON
//     =============================== */
//     protected function buildChartComparison(): array
//     {
//         return [
//             'labels' => ['Bulan Ini', 'Bulan Lalu'],
//             'thisMonth' => [
//                 Payments::whereMonth('created_at', now()->month)
//                     ->whereYear('created_at', now()->year)
//                     ->sum('jumlah'),
//             ],
//             'lastMonth' => [
//                 Payments::whereMonth('created_at', now()->subMonth()->month)
//                     ->whereYear('created_at', now()->subMonth()->year)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }
// }


// namespace App\Services\Dashboard;

// use App\Models\{
//     Payments,
//     LayananPayment,
//     TripExpense,
//     OperationalExpense,
//     Invoices,
//     LayananInvoice,
//     TabunganUmrah,
//     Agent,
//     Branch,
//     Jamaah,
//     MarketingExpenses
// };

// class SuperadminDashboard extends AbstractDashboard
// {

//     public function view(): string
//     {
//         return 'dashboard.superadmin';
//     }
//     protected function title(): string
//     {
//         return 'Dashboard Superadmin';
//     }

//     /* ===============================
//        CARDS
//     =============================== */
//     protected function buildCards(int $month, int $year): array
//     {
//         return [

//             $this->card(
//                 'Omzet Jamaah',
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),
//                 'fa-wallet'
//             ),

//             $this->card(
//                 'Omzet Layanan',
//                 LayananPayment::where('status', 'approved')
//                     ->whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('amount'),
//                 'fa-briefcase'
//             ),

//             $this->card(
//                 'Pengeluaran Jamaah',
//                 TripExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),
//                 'fa-plane-departure'
//             ),

//             $this->card(
//                 'Operasional Kantor',
//                 OperationalExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),
//                 'fa-building'
//             ),

//             $this->card(
//                 'Marketing Spend',
//                 MarketingExpenses::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('biaya'),
//                 'fa-bullhorn'
//             ),

//             $this->card(
//                 'Tagihan Jamaah',
//                 Invoices::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('sisa_tagihan'),
//                 'fa-file-invoice'
//             ),

//             $this->card(
//                 'Tagihan Layanan',
//                 LayananInvoice::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('amount'),
//                 'fa-file-contract'
//             ),

//             $this->card(
//                 'Dana Tabungan',
//                 TabunganUmrah::sum('saldo'),
//                 'fa-piggy-bank'
//             ),

//             $this->card(
//                 'Jamaah Menabung',
//                 TabunganUmrah::distinct('jamaah_id')->count('jamaah_id'),
//                 'fa-user-check'
//             ),

//             $this->card('Total Agen', Agent::count(), 'fa-user-tie'),
//             $this->card('Total Cabang', Branch::count(), 'fa-sitemap'),
//             $this->card('Total Jamaah', Jamaah::count(), 'fa-users'),
//         ];
//     }

//     /* ===============================
//        CHART TOTAL
//     =============================== */
//     protected function buildChart(int $month, int $year): array
//     {
//         return [
//             'labels' => [
//                 'Omzet Jamaah',
//                 'Omzet Layanan',
//                 'Marketing',
//                 'Pengeluaran Jamaah',
//                 'Operasional',
//             ],
//             'values' => [
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),

//                 LayananPayment::where('status', 'approved')
//                     ->whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('amount'),

//                 MarketingExpenses::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('biaya'),

//                 TripExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),

//                 OperationalExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     /* ===============================
//        CHART COMPARISON
//     =============================== */
//     protected function buildChartComparison(): array
//     {
//         $now  = now();
//         $last = now()->subMonth();

//         return [
//             'labels' => ['Omzet Jamaah'],
//             'thisMonth' => [
//                 Payments::whereMonth('created_at', $now->month)
//                     ->whereYear('created_at', $now->year)
//                     ->sum('jumlah'),
//             ],
//             'lastMonth' => [
//                 Payments::whereMonth('created_at', $last->month)
//                     ->whereYear('created_at', $last->year)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }
// }

// namespace App\Services\Dashboard;

// use App\Services\Dashboard\Contracts\DashboardContract;
// use App\Models\Payments;
// use App\Models\LayananPayment;
// use App\Models\TripExpense;
// use App\Models\OperationalExpense;
// use App\Models\Invoices;
// use App\Models\LayananInvoice;
// use App\Models\TabunganUmrah;
// use App\Models\Agent;
// use App\Models\Branch;
// use App\Models\Jamaah;
// use App\Models\MarketingExpenses;
// class SuperadminDashboard implements DashboardContract
// {
//     /* =====================================================
//        VIEW
//     ===================================================== */
//     public function view(): string
//     {
//         return 'dashboard.superadmin';
//     }

//     /* =====================================================
//        CARDS
//     ===================================================== */
//     public function cards(int $month, int $year): array
//     {
//         return [
//             'title' => 'Dashboard Superadmin',
//             'cards' => [

//                 // ===============================
//                 // 📊 PERIODIK (FILTERED)
//                 // ===============================
//                 $this->card(
//                     'Omzet Jamaah',
//                     Payments::whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->sum('jumlah'),
//                     'fa-wallet'
//                 ),

//                 $this->card(
//                     'Omzet Layanan',
//                     LayananPayment::where('status', 'approved')
//                         ->whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->sum('amount'),
//                     'fa-briefcase'
//                 ),

//                 $this->card(
//                     'Pengeluaran Jamaah',
//                     TripExpense::whereMonth('tanggal', $month)
//                         ->whereYear('tanggal', $year)
//                         ->sum('jumlah'),
//                     'fa-plane-departure'
//                 ),

//                 $this->card(
//                     'Operasional Kantor',
//                     OperationalExpense::whereMonth('tanggal', $month)
//                         ->whereYear('tanggal', $year)
//                         ->sum('jumlah'),
//                     'fa-building'
//                 ),

//                 $this->card(
//                     'Marketing Spend',
//                     MarketingExpenses::whereMonth('tanggal', $month)
//                         ->whereYear('tanggal', $year)
//                         ->sum('biaya'),
//                     'fa-bullhorn'
//                 ),


//                 $this->card(
//                     'Tagihan Jamaah',
//                     Invoices::whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->sum('sisa_tagihan'),
//                     'fa-file-invoice'
//                 ),

//                 $this->card(
//                     'Tagihan Layanan',
//                     LayananInvoice::whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->get()
//                         ->sum('sisa'),
//                     'fa-file-contract'
//                 ),

//                 $this->card(
//                     'Dana Tabungan',
//                     TabunganUmrah::whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->sum('saldo'),
//                     'fa-piggy-bank'
//                 ),

//                 $this->card(
//                     'Jamaah Menabung',
//                     TabunganUmrah::whereMonth('created_at', $month)
//                         ->whereYear('created_at', $year)
//                         ->distinct('jamaah_id')
//                         ->count('jamaah_id'),
//                     'fa-user-check'
//                 ),

//                 // ===============================
//                 // 🧩 MASTER (ALL TIME)
//                 // ===============================
//                 $this->card(
//                     'Total Agen',
//                     Agent::count(),
//                     'fa-user-tie'
//                 ),

//                 $this->card(
//                     'Total Cabang',
//                     Branch::count(),
//                     'fa-sitemap'
//                 ),

//                 $this->card(
//                     'Total Jamaah',
//                     Jamaah::count(),
//                     'fa-users'
//                 ),
//             ],
//         ];
//     }

//     /* =====================================================
//        CHART TOTAL (FILTERED)
//     ===================================================== */
//     public function chart(int $month, int $year): array
//     {
//         return [
//             'labels' => [
//                 'Omzet Jamaah',
//                 'Omzet Layanan',
//                 'Biaya Marketing',
//                 'Pengeluaran Jamaah',
//                 'Operasional Kantor',
//             ],
//             'values' => [
//                 // Omzet Jamaah
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),

//                 // Omzet Layanan
//                 LayananPayment::where('status', 'approved')
//                     ->whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('amount'),

//                 // Biaya Marketing
//                 MarketingExpenses::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('biaya'),

//                 // Pengeluaran Jamaah
//                 TripExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),

//                 // Operasional Kantor
//                 OperationalExpense::whereMonth('tanggal', $month)
//                     ->whereYear('tanggal', $year)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     /* =====================================================
//        CHART COMPARISON (BULAN INI vs BULAN LALU)
//     ===================================================== */
//     public function chartComparison(): array
//     {
//         $thisMonth = Payments::whereMonth('created_at', now()->month)
//             ->whereYear('created_at', now()->year)
//             ->sum('jumlah');

//         $lastMonth = Payments::whereMonth('created_at', now()->subMonth()->month)
//             ->whereYear('created_at', now()->subMonth()->year)
//             ->sum('jumlah');

//         return [
//             'labels'     => ['Omzet Jamaah'],
//             'thisMonth'  => [$thisMonth],
//             'lastMonth'  => [$lastMonth],
//         ];
//     }

//     /* =====================================================
//        HELPER
//     ===================================================== */
//     protected function card(string $label, $value, string $icon): array
//     {
//         return compact('label', 'value', 'icon');
//     }
// }
