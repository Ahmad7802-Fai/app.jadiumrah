<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasRoleIsolation
{
    protected static function bootHasRoleIsolation()
    {
        static::addGlobalScope('roleIsolation', function (Builder $builder) {

            $user = Auth::user();

            if (!$user) {
                return;
            }

            // SUPERADMIN & ADMIN_PUSAT lihat semua
            if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
                return;
            }

            // ADMIN CABANG hanya cabangnya
            if ($user->hasRole('ADMIN_CABANG')) {
                $builder->where('branch_id', $user->branch_id);
                return;
            }

            // AGENT hanya jamaah miliknya
            if ($user->hasRole('AGENT')) {
                $builder->where('agent_id', $user->id);
                return;
            }

            // CUSTOMER hanya jamaah sendiri
            $builder->where('user_id', $user->id);
        });
    }
}