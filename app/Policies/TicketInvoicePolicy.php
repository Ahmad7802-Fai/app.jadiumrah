<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TicketInvoice;

class TicketInvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }

    public function view(User $user, TicketInvoice $invoice): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN']);
    }

    public function pay(User $user, TicketInvoice $invoice): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN'])
            && in_array(
                $invoice->status,
                ['UNPAID', 'PARTIAL'],
                true
            );
    }

    public function refund(User $user, TicketInvoice $invoice): bool
    {
        return $user->hasRole(['SUPERADMIN', 'KEUANGAN'])
            && in_array(
                $invoice->status,
                ['PAID', 'PARTIAL'],
                true
            );
    }

}
