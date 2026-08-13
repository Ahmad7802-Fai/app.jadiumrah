<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Paket;

class PaketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('paket.view');
    }

    public function view(User $user, Paket $paket): bool
    {
        return $user->can('paket.view');
    }

    public function create(User $user): bool
    {
        return $user->can('paket.create');
    }

    public function update(User $user, Paket $paket): bool
    {
        return $user->can('paket.update');
    }

    public function delete(User $user, Paket $paket): bool
    {
        return $user->can('paket.delete');
    }
}