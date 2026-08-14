<?php

namespace App\Policies;

use App\Models\TicketPayment;
use App\Models\User;

class TicketPaymentPolicy
{
    public function view(User $user, TicketPayment $payment): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }
}
