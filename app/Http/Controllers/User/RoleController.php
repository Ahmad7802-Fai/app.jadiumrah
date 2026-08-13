<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Roles\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $service
    ) {}

    public function index()
    {
        $roles = Role::withCount('permissions')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $this->service->create($data);

        return redirect()->route('roles.index')
            ->with('success','Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role','permissions','rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array'
        ]);

        $this->service->update($role, $data);

        return redirect()->route('roles.index')
            ->with('success','Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'SUPERADMIN') {
            abort(403, 'Cannot delete SUPERADMIN role.');
        }

        $this->service->delete($role);

        return redirect()->route('roles.index')
            ->with('success','Role deleted.');
    }

    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }
}