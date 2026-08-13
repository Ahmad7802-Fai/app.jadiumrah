<?php

namespace App\Policies;

use App\Models\Pipeline;
use App\Models\User;

class PipelinePolicy
{
    /* =====================================================
     | VIEW PIPELINE (KANBAN HEADER / MASTER)
     ===================================================== */
    public function viewAny(User $user): bool
    {
        return $user->isActive()
            && ($user->isPusat() || $user->isCabang() || $user->isAgent());
    }

    public function view(User $user, Pipeline $pipeline): bool
    {
        return $this->viewAny($user);
    }

    /* =====================================================
     | MANAGE PIPELINE MASTER (OPTIONAL)
     | biasanya hanya pusat
     ===================================================== */
    public function create(User $user): bool
    {
        return $user->isPusat();
    }

    public function update(User $user, Pipeline $pipeline): bool
    {
        return $user->isPusat();
    }

    public function delete(User $user, Pipeline $pipeline): bool
    {
        return $user->isPusat();
    }
}
