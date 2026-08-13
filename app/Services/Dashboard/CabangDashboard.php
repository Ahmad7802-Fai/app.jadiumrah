<?php

namespace App\Services\Dashboard;

use App\Models\Agent;
use App\Models\Jamaah;

class CabangDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'cabang.dashboard.index';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard Cabang';
    }

    /* ===============================
       CARDS
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        return [
            $this->card(
                'Total Jamaah',
                Jamaah::count(),
                'fa-users'
            ),

            $this->card(
                'Jamaah Aktif',
                Jamaah::whereNotNull('agent_id')->count(),
                'fa-user-check'
            ),

            $this->card(
                'Jamaah Lunas',
                Jamaah::where('sisa', '<=', 0)->count(),
                'fa-check-circle',
                ['variant' => 'card-stat-success']
            ),

            $this->card(
                'Belum Lunas',
                Jamaah::where(function ($q) {
                    $q->whereNull('sisa')->orWhere('sisa', '>', 0);
                })->count(),
                'fa-clock',
                ['variant' => 'card-stat-danger']
            ),
        ];
    }

    /* ===============================
       CHART UTAMA
       (STATUS PEMBAYARAN)
    =============================== */
    protected function chartLabels(): array
    {
        return ['Lunas', 'Belum Lunas'];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        return [
            Jamaah::where('sisa', '<=', 0)->count(),
            Jamaah::where(function ($q) {
                $q->whereNull('sisa')->orWhere('sisa', '>', 0);
            })->count(),
        ];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        // Cabang tidak butuh data bulan lalu detail
        return [0, 0];
    }

    /* ===============================
       CHART COMPARISON
       (WAJIB ADA, WALAU SIMPLE)
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

    /* ===============================
       EXTRA DATA (KHUSUS CABANG)
    =============================== */
    public function jamaahPerAgent(): array
    {
        return Agent::with('user')
            ->withCount('jamaah')
            ->get()
            ->map(fn ($a) => [
                'name'  => $a->user?->nama ?? 'Tanpa Nama',
                'total' => (int) $a->jamaah_count,
            ])
            ->values()
            ->toArray();
    }
}
