<?php

namespace App\Services\Roles;

use Spatie\Permission\Models\Role;

class RoleQueryService
{
    public function all()
    {
        return Role::with('permissions')->get();
    }

    public function find(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }
}