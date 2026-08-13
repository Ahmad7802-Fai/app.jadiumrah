<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch()
    {
        static::addGlobalScope('branch_scope', function (Builder $builder) {

            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->hasRole('SUPERADMIN')) {
                return; // Superadmin lihat semua
            }

            if (in_array('branch_id', (new static)->getFillable())) {
                $builder->where('branch_id', $user->branch_id);
            }
        });
    }
}