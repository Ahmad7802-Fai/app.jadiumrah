<?php

namespace App\Services\Contracts;

use Spatie\Permission\Models\Role;

interface RoleServiceInterface
{
    public function create(array $data): Role;

    public function update(Role $role, array $data): Role;

    public function delete(Role $role): void;
}