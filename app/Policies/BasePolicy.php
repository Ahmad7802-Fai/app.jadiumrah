<?php

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    /**
     * Auto bypass untuk SUPERADMIN
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        return null; // lanjut ke method policy
    }

    /**
     * Helper untuk permission check
     */
    protected function allow(User $user, string $permission): bool
    {
        return $user->can($permission);
    }
}