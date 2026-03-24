<?php

namespace App\Services\Roles;

use App\Services\Contracts\RoleServiceInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleServiceInterface
{
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {

            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {

            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role) {
            $role->delete();
        });
    }
}