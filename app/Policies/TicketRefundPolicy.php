<?php

namespace App\Policies;

use App\Models\TicketRefund;
use App\Models\User;

class TicketRefundPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'SUPERADMIN',
            'KEUANGAN',
        ]);
    }

    public function approve(
        User $user,
        TicketRefund $refund
    ): bool {
        return $user->hasRole([
            'SUPERADMIN',
            'KEUANGAN',
        ]) && $refund->approval_status === 'PENDING';
    }
}
