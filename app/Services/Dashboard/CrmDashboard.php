<?php

namespace App\Services\Dashboard;

use App\Models\{
    Pipeline,
    Lead,
    LeadClosing
};

class CrmDashboard extends AbstractDashboard
{
    /* ===============================
       VIEW
    =============================== */
    public function view(): string
    {
        return 'dashboard.crm';
    }

    /* ===============================
       TITLE
    =============================== */
    protected function title(): string
    {
        return 'Dashboard CRM';
    }

    /* ===============================
       CARDS
       (LEAD PER PIPELINE + CLOSING)
    =============================== */
    protected function buildCards(int $month, int $year): array
    {
        $cards = [];

        $pipelines = Pipeline::orderBy('urutan')->get();

        foreach ($pipelines as $pipeline) {
            $cards[] = $this->card(
                $pipeline->tahap,
                Lead::where('pipeline_id', $pipeline->id)->count(),
                'fa-circle-dot'
            );
        }
        $cards[] = $this->card(
            'Closing',
            Lead::count(),
            'fa-user-plus',
            ['variant' => 'card-stat-success']
        );
        // TOTAL CLOSING
        $cards[] = $this->card(
            'Closing',
            LeadClosing::count(),
            'fa-check-circle',
            ['variant' => 'card-stat-success']
        );

        return $cards;
    }

    /* ===============================
       CHART UTAMA
       (DISTRIBUSI LEAD PER PIPELINE)
    =============================== */
    protected function chartLabels(): array
    {
        return Pipeline::orderBy('urutan')
            ->pluck('tahap')
            ->toArray();
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        return Pipeline::orderBy('urutan')
            ->get()
            ->map(fn ($pipeline) =>
                Lead::where('pipeline_id', $pipeline->id)->count()
            )
            ->toArray();
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        // CRM tidak butuh perbandingan pipeline bulan lalu
        // tapi method wajib ada
        return array_fill(0, Pipeline::count(), 0);
    }

    /* ===============================
       CHART COMPARISON
       (LEAD MASUK BULANAN)
    =============================== */
    protected function chartComparisonThisMonth(): array
    {
        return [
            Lead::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    protected function chartComparisonLastMonth(): array
    {
        $last = now()->subMonth();

        return [
            Lead::whereMonth('created_at', $last->month)
                ->whereYear('created_at', $last->year)
                ->count(),
        ];
    }
}
