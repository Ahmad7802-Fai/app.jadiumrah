<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BranchScope
{
    protected static function bootBranchScope()
    {
        static::addGlobalScope('branch', function (Builder $builder) {

            $user = auth()->user();

            if (!$user) return;

            if ($user->isSuperAdmin() || $user->isAdminPusat()) {
                return;
            }

            if ($user->isAdminCabang()) {
                $builder->where('branch_id', $user->branch_id);
            }

            if ($user->isAgent()) {
                $builder->where('agent_id', $user->id);
            }
        });
    }
}