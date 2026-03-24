<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucher;

class VoucherPolicy
{
    public function viewAny(User $user)
    {
        return $user->can('voucher.view');
    }

    public function view(User $user, Voucher $voucher)
    {
        return $user->can('voucher.view');
    }

    public function create(User $user)
    {
        return $user->can('voucher.create');
    }

    public function update(User $user, Voucher $voucher)
    {
        return $user->can('voucher.update');
    }

    public function delete(User $user, Voucher $voucher)
    {
        return $user->can('voucher.delete');
    }
}