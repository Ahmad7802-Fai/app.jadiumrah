<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoices;

class InvoicePolicy
{
    public function print(User $user, Invoices $invoice): bool
    {
        return
            in_array($user->role, [
                'SUPERADMIN',
                'KEUANGAN',
                'ADMIN',
                'SALES',
            ], true)
            && !empty($invoice->nomor_invoice)
            && in_array($invoice->status, ['CICILAN', 'LUNAS']);
    }
}
