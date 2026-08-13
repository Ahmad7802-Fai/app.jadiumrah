<?php

namespace App\Services\Branches;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class BranchQueryService
{
    public function all()
    {
        $user = Auth::user();

        // SUPERADMIN → lihat semua
        if ($user->hasRole('SUPERADMIN')) {
            return Branch::latest()->paginate(15);
        }

        // ADMIN / CABANG → hanya branch dia
        return Branch::where('id', $user->branch_id)
            ->paginate(15);
    }

    public function find(int $id): Branch
    {
        return Branch::findOrFail($id);
    }
}