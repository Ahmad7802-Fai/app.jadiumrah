<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchIsolation
{
    public static function apply(Builder $query): Builder
    {
        if (!Auth::check()) {
            return $query;
        }

        $user = Auth::user();

        // Global roles → no filter
        if ($user->hasAnyRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'KEUANGAN_PUSAT'
        ])) {
            return $query;
        }

        // Agent → only own data
        if ($user->hasRole('AGENT')) {
            return $query->where('agent_id', $user->agent?->id);
        }

        // Branch roles
        if ($user->branch_id) {
            return $query->where('branch_id', $user->branch_id);
        }

        return $query;
    }
}