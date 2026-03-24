<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch()
    {
        /*
        |--------------------------------------------------------------------------
        | Auto Inject branch_id Saat Create
        |--------------------------------------------------------------------------
        */
        static::creating(function ($model) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            // SUPERADMIN tidak dipaksa punya branch
            if (!$user->hasRole('SUPERADMIN')) {
                $model->branch_id = $user->branch_id;
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Global Scope: Filter Data Berdasarkan Branch
        |--------------------------------------------------------------------------
        */
        static::addGlobalScope('branch', function (Builder $builder) {

            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();

            if (!$user->hasRole('SUPERADMIN')) {
                $builder->where(
                    $builder->getModel()->getTable() . '.branch_id',
                    $user->branch_id
                );
            }
        });
    }
}