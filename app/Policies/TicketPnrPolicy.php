<?php

namespace App\Policies;

use App\Models\TicketPnr;
use App\Models\User;

class TicketPnrPolicy
{
    public function view(User $user, TicketPnr $pnr): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }

    public function update(User $user, TicketPnr $pnr): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN'])
            && $pnr->status !== 'ISSUED';
    }

    public function delete(User $user, TicketPnr $pnr): bool
    {
        return $user->hasRole('SUPERADMIN');
    }

    public function confirm(User $user, TicketPnr $pnr): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN'])
            && $pnr->status === 'ON_FLOW';
    }
}
