<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TicketInvoice;

class TicketInvoicePolicy
{
    public function view(User $user, TicketInvoice $invoice): bool
    {
        return $user->isSuperAdmin()
            || $invoice->pnr->agent_id === $user->agent_id
            || $invoice->pnr->branch_id === $user->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    public function pay(User $user, TicketInvoice $invoice): bool
    {
        return $invoice->status !== 'PAID'
            && $user->hasRole(['SUPERADMIN', 'ADMIN', 'FINANCE']);
    }

    public function cancel(User $user, TicketInvoice $invoice): bool
    {
        return $user->isSuperAdmin()
            && $invoice->paid_amount === 0;
    }

    public function refund(User $user, TicketInvoice $invoice): bool
    {
        return in_array($invoice->status, ['PAID', 'PARTIAL']);
    }

}
