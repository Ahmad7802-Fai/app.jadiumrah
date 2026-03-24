<?php

namespace App\Services\Commission;

use App\Models\Branch;
use App\Models\CommissionScheme;
use App\Models\CommissionCompanyRule;
use App\Models\CommissionBranchRule;
use Illuminate\Validation\ValidationException;

class BranchCommissionService
{
    private function getActiveScheme(): CommissionScheme
    {
        $scheme = CommissionScheme::where('is_active', true)->first();

        if (!$scheme) {
            throw ValidationException::withMessages([
                'scheme' => 'No active commission scheme found.'
            ]);
        }

        return $scheme;
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL CONFIG DATA
    |--------------------------------------------------------------------------
    */

    public function getAllBranchConfigs()
    {
        $scheme = $this->getActiveScheme();

        $branches = Branch::with([
            'companyRules' => fn ($q) => $q->where('commission_scheme_id', $scheme->id),
            'branchRules'  => fn ($q) => $q->where('commission_scheme_id', $scheme->id),
        ])->get();

        return compact('scheme', 'branches');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE COMPANY RULE
    |--------------------------------------------------------------------------
    */

    public function updateCompany(int $branchId, float $amount): void
    {
        $scheme = $this->getActiveScheme();

        CommissionCompanyRule::updateOrCreate(
            [
                'commission_scheme_id' => $scheme->id,
                'branch_id' => $branchId,
                'paket_id' => null,
            ],
            [
                'amount_per_closing' => $amount,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE AGENT RULE
    |--------------------------------------------------------------------------
    */

    public function updateAgent(int $branchId, float $percentage): void
    {
        if ($percentage > 100) {
            throw ValidationException::withMessages([
                'agent_percentage' => 'Agent percentage cannot exceed 100%.'
            ]);
        }

        $scheme = $this->getActiveScheme();

        CommissionBranchRule::updateOrCreate(
            [
                'commission_scheme_id' => $scheme->id,
                'branch_id' => $branchId,
                'paket_id' => null,
            ],
            [
                'agent_percentage' => $percentage,
            ]
        );
    }
}