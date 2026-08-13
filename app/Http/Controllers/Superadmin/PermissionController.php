<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return view('superadmin.permissions.index', [
            'title' => 'Manajemen Permission',
            'permissions' => Permission::all()
        ]);
    }

    public function create()
    {
        return view('superadmin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'perm_key' => 'required|unique:permissions',
            'perm_name' => 'required',
        ]);

        Permission::create($request->only('perm_key', 'perm_name'));

        return redirect()->route('superadmin.permissions.index')
            ->with('success','Permission berhasil ditambahkan');
    }

    public function edit(Permission $permission)
    {
        return view('superadmin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'perm_key' => 'required|unique:permissions,perm_key,' . $permission->id,
            'perm_name' => 'required',
        ]);

        $permission->update($request->only('perm_key','perm_name'));

        return redirect()->route('superadmin.permissions.index')
            ->with('success', 'Permission berhasil diperbarui');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('superadmin.permissions.index')
            ->with('success', 'Permission berhasil dihapus');
    }
}
