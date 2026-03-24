<?php

namespace App\Services\Dashboard;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\User;
use App\Models\CommissionLog;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return match (true) {

            $user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT', 'KEUANGAN_PUSAT'])
                => $this->pusatStats(),

            $user->hasRole(['ADMIN_CABANG', 'KEUANGAN_CABANG', 'OPERATOR_CABANG', 'CRM_CABANG'])
                => $this->cabangStats($user->branch_id),

            $user->hasRole('AGENT')
                => $this->agentStats($user->id),

            $user->hasRole('JAMAAH')
                => $this->jamaahStats($user->id),

            default => [],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | PUSAT (GLOBAL VIEW)
    |--------------------------------------------------------------------------
    */

    private function pusatStats(): array
    {
        $bookingQuery = Booking::query();
        $commissionQuery = CommissionLog::query();

        return [
            'total_branches'   => Branch::count(),
            'total_users'      => User::count(),

            'total_bookings'   => $bookingQuery->count(),
            'confirmed_bookings' => (clone $bookingQuery)
                ->where('status', 'confirmed')
                ->count(),

            'total_revenue'    => $bookingQuery->sum('total_amount'),

            'total_company_commission' => $commissionQuery->sum('company_amount'),
            'total_branch_commission'  => $commissionQuery->sum('branch_amount'),
            'total_agent_commission'   => $commissionQuery->sum('agent_amount'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CABANG (SCOPED)
    |--------------------------------------------------------------------------
    */

    private function cabangStats(?int $branchId): array
    {
        if (!$branchId) {
            return [];
        }

        $bookingQuery = Booking::where('branch_id', $branchId);
        $commissionQuery = CommissionLog::where('branch_id', $branchId);

        return [
            'total_bookings' => $bookingQuery->count(),

            'confirmed_bookings' => (clone $bookingQuery)
                ->where('status', 'confirmed')
                ->count(),

            'total_revenue' => $bookingQuery->sum('total_amount'),

            'branch_commission_received' => $commissionQuery->sum('branch_amount'),

            'agent_commission_paid' => $commissionQuery->sum('agent_amount'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | AGENT
    |--------------------------------------------------------------------------
    */

    private function agentStats(int $userId): array
    {
        $bookingQuery = Booking::where('agent_id', $userId);
        $commissionQuery = CommissionLog::where('agent_id', $userId);

        return [
            'my_bookings' => $bookingQuery->count(),

            'confirmed_bookings' => (clone $bookingQuery)
                ->where('status', 'confirmed')
                ->count(),

            'my_total_revenue' => $bookingQuery->sum('total_amount'),

            'my_commission' => $commissionQuery->sum('agent_amount'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | JAMAAH
    |--------------------------------------------------------------------------
    */

    private function jamaahStats(int $userId): array
    {
        return [
            'my_bookings' => Booking::whereHas('jamaah', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
        ];
    }
}