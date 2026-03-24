<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\Contracts\UserQueryServiceInterface;

class UserQueryService implements UserQueryServiceInterface
{
    public function all()
    {
        $user = Auth::user();

        if (!$user) {
            return User::query()->paginate(15);
        }

        // SUPERADMIN → lihat semua
        if ($user->hasRole('SUPERADMIN')) {
            return User::with('roles', 'branch')
                ->latest()
                ->paginate(15);
        }

        // ADMIN / CABANG → hanya branch dia
        if ($user->hasAnyRole(['ADMIN', 'CABANG'])) {
            return User::with('roles', 'branch')
                ->where('branch_id', $user->branch_id)
                ->latest()
                ->paginate(15);
        }

        // AGENT / JAMAAH → hanya dirinya sendiri
        return User::with('roles', 'branch')
            ->where('id', $user->id)
            ->paginate(15);
    }
}