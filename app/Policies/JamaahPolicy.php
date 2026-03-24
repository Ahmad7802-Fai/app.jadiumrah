<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jamaah;

class JamaahPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('jamaah.view');
    }

    public function view(User $user, Jamaah $jamaah)
    {
        if ($user->isSuperAdmin() || $user->isAdminPusat()) {
            return true;
        }

        if ($user->isAdminCabang()) {
            return $jamaah->branch_id === $user->branch_id;
        }

        if ($user->isAgent()) {
            return $jamaah->agent_id === $user->id;
        }

        if ($user->isCustomer()) {
            return $jamaah->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('jamaah.create');
    }

    public function update(User $user, Jamaah $jamaah): bool
    {
        if (!$user->can('jamaah.update')) {
            return false;
        }

        return $this->view($user, $jamaah);
    }

    public function delete(User $user, Jamaah $jamaah): bool
    {
        if (!$user->can('jamaah.delete')) {
            return false;
        }

        return $this->view($user, $jamaah);
    }

    public function approve(User $user, Jamaah $jamaah): bool
    {
        if (!$user->can('jamaah.approve')) {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return true;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $jamaah->branch_id === $user->branch_id;
        }

        return false;
    }
}