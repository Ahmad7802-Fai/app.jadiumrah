<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TicketPnr;

class TicketPnrPolicy
{
    public function view(User $user, TicketPnr $pnr): bool
    {
        if ($user->isSuperAdmin()) return true;

        if ($user->agent_id && $pnr->agent_id === $user->agent_id) {
            return true;
        }

        if ($user->branch_id && $pnr->branch_id === $user->branch_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    public function update(User $user, TicketPnr $pnr)
    {
        if ($pnr->status === 'ISSUED') {
            return false;
        }

        return true;
    }


    public function delete(User $user, TicketPnr $pnr): bool
    {
        return $user->isSuperAdmin();
    }

    public function confirm(User $user, TicketPnr $pnr): bool
    {
        return $user->can('update-ticketing')
            && $pnr->status === 'ON_FLOW';
    }

}
