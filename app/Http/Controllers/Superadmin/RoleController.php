<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->q;
        $roles = Role::when($q, fn($qry) => $qry->where('role_name','like',"%{$q}%"))
                     ->orderBy('created_at','desc')
                     ->paginate(20);

        return view('superadmin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('perm_name')->get();
        return view('superadmin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name'   => 'required|string|max:100|unique:roles,role_name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'role_name' => $data['role_name'],
            'description'=> $data['description'] ?? null,
        ]);

        // sync permissions
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('perm_name')->get();
        return view('superadmin.roles.edit', compact('role','permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'role_name'   => 'required|string|max:100|unique:roles,role_name,'.$role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'role_name' => $data['role_name'],
            'description'=> $data['description'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        // optional: prevent deleting builtin roles
        if (in_array($role->role_name, ['SUPERADMIN'])) {
            return back()->with('error','Role ini tidak boleh dihapus.');
        }

        $role->permissions()->sync([]); // clear
        $role->delete();

        return back()->with('success','Role dihapus.');
    }

    // optionally: show() omitted
}
