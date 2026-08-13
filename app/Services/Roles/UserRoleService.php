<?php

namespace App\Services\Roles;

use App\Models\User;
use App\Services\Contracts\UserRoleServiceInterface;

class UserRoleService implements UserRoleServiceInterface
{
    public function sync(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    public function assign(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    public function remove(User $user, string $role): void
    {
        $user->removeRole($role);
    }
}