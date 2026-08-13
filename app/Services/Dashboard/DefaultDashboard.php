<?php

namespace App\Services\Dashboard;

class DefaultDashboard extends AbstractDashboard
{
    public function view(): string
    {
        return 'dashboard.default';
    }

    protected function title(): string
    {
        return 'Dashboard';
    }

    protected function buildCards(int $month, int $year): array
    {
        return [];
    }

    protected function chartLabels(): array
    {
        return [];
    }

    protected function chartThisMonth(int $month, int $year): array
    {
        return [];
    }

    protected function chartLastMonth(int $month, int $year): array
    {
        return [];
    }

    protected function chartComparisonThisMonth(): array
    {
        return [];
    }

    protected function chartComparisonLastMonth(): array
    {
        return [];
    }
}
