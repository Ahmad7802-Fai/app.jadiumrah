<?php

namespace App\Policies;

use App\Models\LeadClosing;
use App\Models\User;

class LeadClosingPolicy
{
    /* ================= VIEW ================= */

    public function view(User $user, LeadClosing $closing): bool
    {
        if ($user->isPusat()) {
            return true;
        }

        if ($user->isCabang()) {
            return (int) $closing->branch_id === (int) $user->branch_id;
        }

        if ($user->isAgent()) {
            return (int) $closing->agent_id === (int) optional($user->agent)->id;
        }

        return false;
    }

    /* ================= CREATE ================= */

    public function create(User $user): bool
    {
        return $user->isActive() && (
            $user->isPusat() ||
            $user->isCabang() ||
            $user->isAgent()
        );
    }

    /* ================= APPROVE ================= */

    public function approve(User $user, LeadClosing $closing): bool
    {
        return $user->isPusat()
            && $closing->status === 'DRAFT';
    }

    /* ================= UPDATE ================= */

    public function update(User $user, LeadClosing $closing): bool
    {
        if ($closing->status !== 'DRAFT') {
            return false;
        }

        if ($user->isPusat()) {
            return true;
        }

        if ($user->isCabang()) {
            return (int) $closing->branch_id === (int) $user->branch_id;
        }

        if ($user->isAgent()) {
            return (int) $closing->agent_id === (int) optional($user->agent)->id;
        }

        return false;
    }
}
