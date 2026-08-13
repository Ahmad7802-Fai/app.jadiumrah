<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MarketingCampaign;

class CampaignPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW ANY
    |--------------------------------------------------------------------------
    */
    public function viewAny(User $user): bool
    {
        return $user->can('campaign.view');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */
    public function view(User $user, MarketingCampaign $campaign): bool
    {
        return $user->can('campaign.view');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(User $user): bool
    {
        return $user->can('campaign.create');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(User $user, MarketingCampaign $campaign): bool
    {
        if (! $user->can('campaign.update')) {
            return false;
        }

        // Campaign finished tidak bisa diupdate
        if ($campaign->status === 'finished') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, MarketingCampaign $campaign): bool
    {
        if (! $user->can('campaign.delete')) {
            return false;
        }

        // Campaign aktif tidak boleh dihapus
        if ($campaign->status === 'active') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE
    |--------------------------------------------------------------------------
    */
    public function activate(User $user, MarketingCampaign $campaign): bool
    {
        return $user->can('campaign.update')
            && $campaign->status === 'draft';
    }

    /*
    |--------------------------------------------------------------------------
    | FINISH
    |--------------------------------------------------------------------------
    */
    public function finish(User $user, MarketingCampaign $campaign): bool
    {
        return $user->can('campaign.update')
            && $campaign->status === 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(User $user, MarketingCampaign $campaign): bool
    {
        return $user->can('campaign.update')
            && $campaign->status !== 'finished';
    }
}