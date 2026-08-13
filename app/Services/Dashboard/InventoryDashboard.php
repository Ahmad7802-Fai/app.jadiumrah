<?php

namespace App\Services\Dashboard;

use App\Models\{
    Item,
    Stock,
    StockMutation
};

class InventoryDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'dashboard.inventory';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard Inventory';
    }

    /* ===============================
       CARDS
       (KONDISI STOK & AKTIVITAS)
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        return [

            $this->card(
                'Total Item',
                Item::count(),
                'fa-boxes'
            ),

            $this->card(
                'Low Stock (<10)',
                Stock::where('stok', '<', 10)->count(),
                'fa-exclamation-circle',
                ['variant' => 'card-stat-danger']
            ),

            $this->card(
                'Mutasi Stok',
                StockMutation::count(),
                'fa-random'
            ),

            $this->card(
                'Total Stok',
                Stock::sum('stok'),
                'fa-warehouse'
            ),
        ];
    }

    /* ===============================
       CHART UTAMA
       (KONDISI INVENTORY)
    =============================== */
    protected function chartLabels(): array
    {
        return [
            'Item',
            'Low Stock',
            'Mutasi',
            'Total Stok',
        ];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        // Inventory → data bersifat realtime / akumulatif
        return [
            Item::count(),
            Stock::where('stok', '<', 10)->count(),
            StockMutation::count(),
            Stock::sum('stok'),
        ];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        // Untuk menjaga struktur chart tetap konsisten
        return [0, 0, 0, 0];
    }

    /* ===============================
       CHART COMPARISON
       (MUTASI STOK BULANAN)
    =============================== */
    protected function chartComparisonThisMonth(): array
    {
        return [
            StockMutation::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    protected function chartComparisonLastMonth(): array
    {
        $last = now()->subMonth();

        return [
            StockMutation::whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->count(),
        ];
    }
}
