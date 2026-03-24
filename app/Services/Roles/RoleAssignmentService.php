<?php

namespace App\Services\Roles;

use App\Models\User;

class RoleAssignmentService
{
    public function assign(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }
}