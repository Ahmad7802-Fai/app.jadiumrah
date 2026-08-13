<?php

namespace App\Policies;

use App\Models\CommissionScheme;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommissionSchemePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->can('commission.view');
    }

    public function create(User $user)
    {
        return $user->can('commission.create');
    }

    public function update(User $user)
    {
        return $user->can('commission.update');
    }

    public function delete(User $user)
    {
        return $user->hasRole('SUPERADMIN');
    }

}
