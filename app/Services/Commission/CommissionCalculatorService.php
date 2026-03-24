<?php

namespace App\Services\Commission;

use App\Models\Booking;
use App\Models\CommissionScheme;
use App\Models\CommissionCompanyRule;
use App\Models\CommissionBranchRule;
use App\Models\AgentTier;
use App\Models\CommissionLog;
use Illuminate\Support\Facades\DB;

class CommissionCalculatorService
{
    public function calculate(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {

            $booking = Booking::where('id', $booking->id)
                ->with('jamaahs')
                ->lockForUpdate()
                ->firstOrFail();

            if ($booking->status !== 'confirmed') {
                return;
            }

            $scheme = $this->getActiveScheme();

            foreach ($booking->jamaahs as $jamaah) {

                // ✅ Prevent double calculation per seat
                if ($this->alreadyCalculated($booking->id, $jamaah->id)) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 🔹 SOURCE OF TRUTH = JAMAAH
                |--------------------------------------------------------------------------
                */

                $branchId = $jamaah->branch_id;
                $agentId  = $jamaah->agent_id;

                // ❌ Jika jamaah murni admin pusat (tanpa cabang)
                if (!$branchId) {
                    continue; // Tidak ada komisi sama sekali
                }

                /*
                |--------------------------------------------------------------------------
                | 1️⃣ COMPANY BASE COMMISSION (PER SEAT)
                |--------------------------------------------------------------------------
                */

                $companyAmount = $this->getCompanyAmount(
                    $scheme,
                    $branchId,
                    $booking->paket_id
                );

                if ($companyAmount <= 0) {
                    continue; // Tidak ada rule → skip saja
                }

                $branchAmount = $companyAmount;
                $agentAmount  = 0;

                /*
                |--------------------------------------------------------------------------
                | 2️⃣ AGENT SPLIT (IF EXISTS)
                |--------------------------------------------------------------------------
                */

                if ($agentId) {

                    $agentPercentage = $this->getAgentPercentage(
                        $scheme,
                        $branchId,
                        $booking->paket_id
                    );

                    if ($agentPercentage > 0) {
                        $agentAmount = ($companyAmount * $agentPercentage) / 100;
                        $branchAmount -= $agentAmount;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 3️⃣ TIER BONUS
                    |--------------------------------------------------------------------------
                    */

                    $bonus = $this->calculateTierBonus(
                        $scheme,
                        $agentId,
                        $companyAmount
                    );

                    if ($bonus > 0) {
                        $agentAmount += $bonus;
                        $branchAmount -= $bonus;
                    }
                }

                if ($branchAmount < 0) {
                    $branchAmount = 0;
                }

                /*
                |--------------------------------------------------------------------------
                | 4️⃣ SAVE PER SEAT LOG
                |--------------------------------------------------------------------------
                */

                CommissionLog::create([
                    'commission_scheme_id' => $scheme->id,
                    'booking_id'           => $booking->id,
                    'jamaah_id'            => $jamaah->id,
                    'branch_id'            => $branchId,
                    'agent_id'             => $agentId,
                    'company_amount'       => $companyAmount,
                    'branch_amount'        => $branchAmount,
                    'agent_amount'         => $agentAmount,
                ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function alreadyCalculated(int $bookingId, int $jamaahId): bool
    {
        return CommissionLog::where('booking_id', $bookingId)
            ->where('jamaah_id', $jamaahId)
            ->exists();
    }

    private function getActiveScheme(): CommissionScheme
    {
        return CommissionScheme::where('is_active', true)
            ->firstOrFail();
    }

    private function getCompanyAmount(
        CommissionScheme $scheme,
        int $branchId,
        int $paketId
    ): float {

        $rule = CommissionCompanyRule::where('commission_scheme_id', $scheme->id)
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($paketId) {
                $q->whereNull('paket_id')
                  ->orWhere('paket_id', $paketId);
            })
            ->first();

        return $rule?->amount_per_closing ?? 0;
    }

    private function getAgentPercentage(
        CommissionScheme $scheme,
        int $branchId,
        int $paketId
    ): float {

        $rule = CommissionBranchRule::where('commission_scheme_id', $scheme->id)
            ->where('branch_id', $branchId)
            ->where(function ($q) use ($paketId) {
                $q->whereNull('paket_id')
                  ->orWhere('paket_id', $paketId);
            })
            ->first();

        return $rule?->agent_percentage ?? 0;
    }

    private function calculateTierBonus(
        CommissionScheme $scheme,
        int $agentId,
        float $companyAmount
    ): float {

        $closingCount = Booking::whereHas('jamaahs', function ($q) use ($agentId) {
                $q->where('agent_id', $agentId);
            })
            ->where('status', 'confirmed')
            ->whereYear('created_at', $scheme->year)
            ->count();

        $tier = AgentTier::where('min_closing', '<=', $closingCount)
            ->where('max_closing', '>=', $closingCount)
            ->first();

        if (!$tier) {
            return 0;
        }

        return ($companyAmount * $tier->bonus_percentage) / 100;
    }

} 

