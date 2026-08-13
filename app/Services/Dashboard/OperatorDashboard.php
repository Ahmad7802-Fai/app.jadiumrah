<?php

namespace App\Services\Dashboard;

use App\Models\{
    Jamaah,
    Keberangkatan,
    Visa
};

class OperatorDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'dashboard.operator';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard Operator';
    }

    /* ===============================
       CARDS
       (OPERASIONAL HARIAN)
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        return [

            $this->card(
                'Jamaah Terdaftar',
                Jamaah::count(),
                'fa-users'
            ),

            $this->card(
                'Keberangkatan',
                Keberangkatan::count(),
                'fa-plane-departure'
            ),

            $this->card(
                'Visa Diproses',
                Visa::where('status', 'Diproses')->count(),
                'fa-passport'
            ),

            $this->card(
                'Visa Selesai',
                Visa::where('status', 'Selesai')->count(),
                'fa-check-circle'
            ),
        ];
    }

    /* ===============================
       CHART UTAMA
       (DISTRIBUSI DATA OPERASIONAL)
    =============================== */
    protected function chartLabels(): array
    {
        return [
            'Jamaah',
            'Visa Diproses',
            'Visa Selesai',
            'Keberangkatan',
        ];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        // Operator fokus data realtime / all time
        return [
            Jamaah::count(),
            Visa::where('status', 'Diproses')->count(),
            Visa::where('status', 'Selesai')->count(),
            Keberangkatan::count(),
        ];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        // Untuk konsistensi struktur chart
        return [0, 0, 0, 0];
    }

    /* ===============================
       CHART COMPARISON
       (JAMAAH BARU BULANAN)
    =============================== */
    protected function chartComparisonThisMonth(): array
    {
        return [
            Jamaah::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    protected function chartComparisonLastMonth(): array
    {
        $last = now()->subMonth();

        return [
            Jamaah::whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->count(),
        ];
    }
}
