<?php

namespace App\Services\Contracts;

use App\Models\User;

interface UserRoleServiceInterface
{
    public function sync(User $user, array $roles): void;

    public function assign(User $user, string $role): void;

    public function remove(User $user, string $role): void;
}