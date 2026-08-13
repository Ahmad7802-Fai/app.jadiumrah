<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Permission;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permissionKey)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Tidak ada user login');
        }

        // Ambil role dari user (enum)
        $roleName = $user->role;

        // Cari role_id di tabel roles
        $role = Role::where('role_name', $roleName)->first();

        if (!$role) {
            abort(403, 'Role tidak ditemukan');
        }

        // Cek apakah role punya permission
        $hasPermission = $role->permissions()
            ->where('perm_key', $permissionKey)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'Anda tidak punya permission: ' . $permissionKey);
        }

        return $next($request);
    }

    
}
