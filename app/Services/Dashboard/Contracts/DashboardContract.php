<?php

namespace App\Services\Dashboard\Contracts;

interface DashboardContract
{
    public function view(): string;

    public function cards(int $month, int $year): array;

    public function chart(int $month, int $year): array;

    public function chartComparison(): array;
}
