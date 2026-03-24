<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cost;

class CostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cost.view');
    }

    public function view(User $user, Cost $cost): bool
    {
        return $user->can('cost.view');
    }

    public function create(User $user): bool
    {
        return $user->can('cost.create');
    }

    public function update(User $user, Cost $cost): bool
    {
        if (!$user->can('cost.update')) {
            return false;
        }

        // hanya boleh edit kalau masih draft
        return $cost->status === 'draft';
    }

    public function approve(User $user, Cost $cost): bool
    {
        return $user->can('cost.approve')
            && $cost->status === 'draft';
    }

    public function delete(User $user, Cost $cost): bool
    {
        return $user->can('cost.delete')
            && $cost->status === 'draft';
    }
}