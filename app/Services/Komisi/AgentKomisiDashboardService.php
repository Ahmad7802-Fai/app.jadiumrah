<?php

namespace App\Services\Komisi;

use App\Models\KomisiLogs;
use App\Models\AgentPayoutRequest;

class AgentKomisiDashboardService
{
    public function getDashboardData(int $agentId): array
    {
        $komisi = $this->paginate($agentId);

        $summary = $this->summary($agentId);

        $hasActivePayout = AgentPayoutRequest::where('agent_id', $agentId)
            ->whereIn('status', [
                AgentPayoutRequest::STATUS_REQUESTED,
                AgentPayoutRequest::STATUS_APPROVED,
            ])
            ->exists();

        return compact('komisi', 'summary', 'hasActivePayout');
    }

    public function summary(int $agentId): array
    {
        $base = KomisiLogs::where('agent_id', $agentId)
            ->whereIn('status', [
                KomisiLogs::STATUS_PENDING,
                KomisiLogs::STATUS_AVAILABLE,
                KomisiLogs::STATUS_PAID,
            ]);

        $pending = (int) (clone $base)
            ->where('status', KomisiLogs::STATUS_PENDING)
            ->sum('komisi_nominal');

        $available = (int) (clone $base)
            ->where('status', KomisiLogs::STATUS_AVAILABLE)
            ->sum('komisi_nominal');

        $paid = (int) (clone $base)
            ->where('status', KomisiLogs::STATUS_PAID)
            ->sum('komisi_nominal');

        return [
            'pending'   => $pending,
            'available' => $available,
            'paid'      => $paid,
            'total'     => $pending + $available + $paid,
        ];
    }

    public function paginate(int $agentId, int $perPage = 15)
    {
        return KomisiLogs::with('jamaah')
            ->where('agent_id', $agentId)
            ->whereIn('status', [
                KomisiLogs::STATUS_PENDING,
                KomisiLogs::STATUS_AVAILABLE,
                KomisiLogs::STATUS_REQUESTED,
                KomisiLogs::STATUS_PAID,
            ])
            ->latest()
            ->paginate($perPage);
    }
}
