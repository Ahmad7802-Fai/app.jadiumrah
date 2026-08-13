<?php

namespace App\Services\Marketing;

use App\Models\MarketingCampaign;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function create(array $data): MarketingCampaign
    {
        return DB::transaction(function () use ($data) {

            $campaign = MarketingCampaign::create([
                'name'             => $data['name'],
                'start_date'       => $data['start_date'],
                'end_date'         => $data['end_date'],
                'target_revenue'   => $data['target_revenue'] ?? 0,
                'budget_marketing' => $data['budget_marketing'] ?? 0,
                'status'           => 'draft',
            ]);

            if (!empty($data['paket_ids'])) {
                $campaign->pakets()->sync($data['paket_ids']);
            }

            return $campaign;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function update(MarketingCampaign $campaign, array $data): MarketingCampaign
    {
        return DB::transaction(function () use ($campaign, $data) {

            if ($campaign->status === 'finished') {
                return $campaign; // tidak bisa edit kalau sudah selesai
            }

            $campaign->update([
                'name'             => $data['name'],
                'start_date'       => $data['start_date'],
                'end_date'         => $data['end_date'],
                'target_revenue'   => $data['target_revenue'] ?? 0,
                'budget_marketing' => $data['budget_marketing'] ?? 0,
            ]);

            if (isset($data['paket_ids'])) {
                $campaign->pakets()->sync($data['paket_ids']);
            }

            return $campaign;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function activate(MarketingCampaign $campaign): MarketingCampaign
    {
        return DB::transaction(function () use ($campaign) {

            if ($campaign->status !== 'draft') {
                return $campaign;
            }

            // hanya boleh 1 campaign aktif di periode yang sama
            MarketingCampaign::where('status', 'active')
                ->where(function ($q) use ($campaign) {
                    $q->whereBetween('start_date', [$campaign->start_date, $campaign->end_date])
                      ->orWhereBetween('end_date', [$campaign->start_date, $campaign->end_date]);
                })
                ->update(['status' => 'finished']);

            $campaign->update([
                'status' => 'active'
            ]);

            return $campaign;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FINISH CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function finish(MarketingCampaign $campaign): MarketingCampaign
    {
        if ($campaign->status !== 'active') {
            return $campaign;
        }

        $campaign->update([
            'status' => 'finished'
        ]);

        return $campaign;
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL CAMPAIGN
    |--------------------------------------------------------------------------
    */
    public function cancel(MarketingCampaign $campaign): MarketingCampaign
    {
        if ($campaign->status === 'finished') {
            return $campaign;
        }

        $campaign->update([
            'status' => 'cancelled'
        ]);

        return $campaign;
    }
}