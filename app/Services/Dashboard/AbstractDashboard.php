<?php

namespace App\Services\Dashboard;

use App\Services\Dashboard\Contracts\DashboardContract;

abstract class AbstractDashboard implements DashboardContract
{

    public function view(): string
    {
        return 'dashboard.default';
    }

    /* ===============================
       CARDS (FINAL)
    =============================== */
    final public function cards(int $month, int $year): array
    {
        return [
            'title' => $this->title(),
            'cards' => $this->buildCards($month, $year),
        ];
    }

    /* ===============================
       CHART UTAMA (FINAL)
    =============================== */
    final public function chart(int $month, int $year): array
    {
        return [
            'labels'    => $this->chartLabels(),
            'thisMonth' => $this->chartThisMonth($month, $year),
            'lastMonth' => $this->chartLastMonth($month, $year),
        ];
    }

    /* ===============================
       CHART COMPARISON (FINAL)
    =============================== */
    final public function chartComparison(): array
    {
        return [
            'labels'    => $this->chartLabels(),
            'thisMonth' => $this->chartComparisonThisMonth(),
            'lastMonth' => $this->chartComparisonLastMonth(),
        ];
    }

    /* ===============================
       WAJIB DIIMPLEMENT DI CHILD
    =============================== */
    abstract protected function title(): string;
    abstract protected function buildCards(int $month, int $year): array;

    abstract protected function chartLabels(): array;
    abstract protected function chartThisMonth(int $month, int $year): array;
    abstract protected function chartLastMonth(int $month, int $year): array;

    abstract protected function chartComparisonThisMonth(): array;
    abstract protected function chartComparisonLastMonth(): array;

    /* ===============================
       HELPER CARD
    =============================== */
    protected function card(
        string $label,
        float|int $value,
        string $icon,
        array $extra = []
    ): array {
        return array_merge([
            'label'   => $label,
            'value'   => $value,
            'display' => number_format($value, 0, ',', '.'),
            'icon'    => $icon,
            'variant' => 'card-stat-primary',
        ], $extra);
    }
}
