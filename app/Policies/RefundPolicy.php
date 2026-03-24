<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Refund;

class RefundPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW ANY
    |--------------------------------------------------------------------------
    */
    public function viewAny(User $user): bool
    {
        return $user->can('refund.view');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */
    public function view(User $user, Refund $refund): bool
    {
        return $user->can('refund.view');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(User $user): bool
    {
        return $user->can('refund.create');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE (Only Pending)
    |--------------------------------------------------------------------------
    */
    public function update(User $user, Refund $refund): bool
    {
        if ($refund->status !== 'pending') {
            return false;
        }

        return $user->can('refund.update');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE (Only Pending)
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, Refund $refund): bool
    {
        if ($refund->status !== 'pending') {
            return false;
        }

        return $user->can('refund.delete');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE (Only Pending)
    |--------------------------------------------------------------------------
    */
    public function approve(User $user, Refund $refund): bool
    {
        if ($refund->status !== 'pending') {
            return false;
        }

        return $user->can('refund.approve');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(User $user, Refund $refund): bool
    {
        if ($refund->status !== 'pending') {
            return false;
        }

        return $user->can('refund.approve');
    }
}