<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;

class BranchPolicy
{
    public function before(User $user): bool|null
    {
        return $user->role === 'SUPERADMIN' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Branch $branch): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Branch $branch): bool
    {
        return true;
    }

    public function delete(User $user, Branch $branch): bool
    {
        return true;
    }

    public function toggle(User $user, Branch $branch): bool
    {
        return true;
    }
}
