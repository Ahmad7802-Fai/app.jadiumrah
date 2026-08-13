<?php

namespace App\Services\Dashboard;

use App\Models\Payments;
use App\Models\LayananPayment;
use App\Models\TripExpense;
use App\Models\OperationalExpense;
use App\Models\Invoices;
use App\Models\LayananInvoice;
use App\Models\MarketingExpenses;

class KeuanganDashboard extends AbstractDashboard
{
    /* =====================================================
       VIEW
    ===================================================== */
    public function view(): string
    {
        return 'dashboard.keuangan';
    }

    /* =====================================================
       TITLE
    ===================================================== */
    protected function title(): string
    {
        return 'Dashboard Keuangan';
    }

    /* =====================================================
       CARDS
    ===================================================== */
    protected function buildCards(int $month, int $year): array
    {
        return [

            // ===============================
            // PEMASUKAN
            // ===============================
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

            // ===============================
            // PENGELUARAN
            // ===============================
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

            // ===============================
            // PIUTANG
            // ===============================
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
                    ->selectRaw('SUM(amount - paid_amount) as sisa')
                    ->value('sisa') ?? 0,
                'fa-file-contract'
            ),
        ];
    }

    /* =====================================================
       CHART TOTAL (BULAN INI)
    ===================================================== */
    protected function buildChart(int $month, int $year): array
    {
        return [
            'labels' => [
                'Omzet Jamaah',
                'Omzet Layanan',
                'Marketing',
                'Pengeluaran Jamaah',
                'Operasional'
            ],
            'values' => [
                Payments::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('jumlah'),

                LayananPayment::where('status', 'approved')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('amount'),

                MarketingExpenses::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('biaya'),

                TripExpense::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('jumlah'),

                OperationalExpense::whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year)
                    ->sum('jumlah'),
            ],
        ];
    }
    /* ===== CHART ===== */

    protected function chartLabels(): array
    {
        return ['Total'];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        return [
            Payments::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('jumlah'),
        ];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        $last = now()->subMonth();

        return [
            Payments::whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->sum('jumlah'),
        ];
    }

    protected function chartComparisonThisMonth(): array
    {
        return $this->chartThisMonth(now()->month, now()->year);
    }

    protected function chartComparisonLastMonth(): array
    {
        return $this->chartLastMonth(now()->month, now()->year);
    }
}

// namespace App\Services\Dashboard;

// use App\Services\Dashboard\Contracts\DashboardContract;
// use App\Models\Payments;
// use App\Models\Invoices;
// use App\Models\Jamaah;
// use App\Models\LayananPayment;
// use App\Models\LayananInvoice;

// class KeuanganDashboard implements DashboardContract
// {
//     /* =====================================================
//      | VIEW
//      ===================================================== */
//     public function view(): string
//     {
//         return 'dashboard.keuangan'; 
//         // sesuaikan dengan blade kamu
//     }

//     /* =====================================================
//      | CARDS (FILTERED)
//      ===================================================== */
//     public function cards(int $month, int $year): array
//     {
//         $incomeJamaah = Payments::whereMonth('created_at', $month)
//             ->whereYear('created_at', $year)
//             ->sum('jumlah');

//         $incomeLayanan = LayananPayment::whereMonth('created_at', $month)
//             ->whereYear('created_at', $year)
//             ->sum('amount');

//         $pendingJamaah = Invoices::sum('sisa_tagihan');
//         $pendingLayanan = LayananInvoice::get()
//             ->sum(fn ($i) => max($i->amount - $i->paid_amount, 0));

//         return [
//             'title' => 'Dashboard Keuangan',
//             'cards' => [
//                 $this->card('Pembayaran Jamaah', $incomeJamaah),
//                 $this->card('Pembayaran Layanan', $incomeLayanan),
//                 $this->card('Total Pembayaran', $incomeJamaah + $incomeLayanan),

//                 $this->card('Tagihan Jamaah Belum Lunas', $pendingJamaah),
//                 $this->card('Tagihan Layanan Belum Lunas', $pendingLayanan),
//                 $this->card('Total Tagihan Belum Lunas', $pendingJamaah + $pendingLayanan),

//                 $this->card('Total Invoice Jamaah', Invoices::count()),
//                 $this->card('Total Invoice Layanan', LayananInvoice::count()),
//                 $this->card('Total Jamaah', Jamaah::count()),
//             ],
//         ];
//     }

//     /* =====================================================
//      | CHART (TOTAL)
//      ===================================================== */
//     public function chart(int $month, int $year): array
//     {
//         return [
//             'labels' => [
//                 'Income Jamaah',
//                 'Income Layanan',
//                 'Pending',
//                 'Invoice'
//             ],
//             'values' => [
//                 Payments::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('jumlah'),

//                 LayananPayment::whereMonth('created_at', $month)
//                     ->whereYear('created_at', $year)
//                     ->sum('amount'),

//                 Invoices::sum('sisa_tagihan'),

//                 Invoices::count() + LayananInvoice::count(),
//             ],
//         ];
//     }

//     /* =====================================================
//      | CHART COMPARISON (THIS vs LAST MONTH)
//      ===================================================== */
//     public function chartComparison(): array
//     {
//         $thisMonth = now();
//         $lastMonth = now()->subMonth();

//         return [
//             'labels' => ['Income Jamaah', 'Income Layanan'],
//             'thisMonth' => [
//                 Payments::whereMonth('created_at', $thisMonth->month)
//                     ->whereYear('created_at', $thisMonth->year)
//                     ->sum('jumlah'),

//                 LayananPayment::whereMonth('created_at', $thisMonth->month)
//                     ->whereYear('created_at', $thisMonth->year)
//                     ->sum('amount'),
//             ],
//             'lastMonth' => [
//                 Payments::whereMonth('created_at', $lastMonth->month)
//                     ->whereYear('created_at', $lastMonth->year)
//                     ->sum('jumlah'),

//                 LayananPayment::whereMonth('created_at', $lastMonth->month)
//                     ->whereYear('created_at', $lastMonth->year)
//                     ->sum('amount'),
//             ],
//         ];
//     }

//     /* =====================================================
//      | HELPER
//      ===================================================== */
//     private function card(string $label, float|int $value): array
//     {
//         return [
//             'label' => $label,
//             'value' => $value,
//             'icon'  => $this->autoIcon($label),
//         ];
//     }

//     private function autoIcon(string $label): string
//     {
//         $l = strtolower($label);

//         return match (true) {
//             str_contains($l, 'jamaah')   => 'fa-user',
//             str_contains($l, 'layanan')  => 'fa-cubes',
//             str_contains($l, 'invoice')  => 'fa-file-invoice',
//             str_contains($l, 'tagihan')  => 'fa-file-invoice-dollar',
//             default                      => 'fa-chart-line',
//         };
//     }
// }
