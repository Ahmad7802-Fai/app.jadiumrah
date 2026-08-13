<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MarketingBanner;

class BannerPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW ANY
    |--------------------------------------------------------------------------
    */
    public function viewAny(User $user): bool
    {
        return $user->can('banner.view');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */
    public function view(User $user, MarketingBanner $banner): bool
    {
        return $user->can('banner.view');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(User $user): bool
    {
        return $user->can('banner.create');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(User $user, MarketingBanner $banner): bool
    {
        if (! $user->can('banner.update')) {
            return false;
        }

        // Banner archived tidak bisa diupdate
        if ($banner->status === 'archived') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, MarketingBanner $banner): bool
    {
        if (! $user->can('banner.delete')) {
            return false;
        }

        // Banner published tidak boleh langsung dihapus
        if ($banner->status === 'published') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH
    |--------------------------------------------------------------------------
    */
    public function publish(User $user, MarketingBanner $banner): bool
    {
        return $user->can('banner.update')
            && $banner->status === 'draft';
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE
    |--------------------------------------------------------------------------
    */
    public function archive(User $user, MarketingBanner $banner): bool
    {
        return $user->can('banner.update')
            && $banner->status === 'published';
    }
}