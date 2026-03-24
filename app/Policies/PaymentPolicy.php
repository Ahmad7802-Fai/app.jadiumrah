<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW ANY
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'ADMIN_CABANG',
            'FINANCE',
            'AGENT',
            'JAMAAH'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return true;
        }

        if ($user->hasRole(['ADMIN_CABANG','FINANCE'])) {
            return $user->branch_id === $payment->branch_id;
        }

        if ($user->hasRole('AGENT')) {
            return $payment->booking->agent_id === $user->id;
        }

        if ($user->hasRole('JAMAAH')) {
            return $payment->booking->user_id === $user->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'ADMIN_CABANG',
            'AGENT',
            'JAMAAH' // 🔥 penting
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

   public function update(User $user, Payment $payment): bool
    {
        if ($payment->status !== 'pending') {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return true;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $user->branch_id === $payment->branch_id;
        }

        if ($user->hasRole('AGENT')) {
            return $payment->booking->agent_id === $user->id;
        }

        // 🔥 JAMAAH hanya boleh update payment miliknya sendiri
        if ($user->hasRole('JAMAAH')) {
            return $payment->booking->user_id === $user->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(User $user, Payment $payment): bool
    {
        if ($payment->status !== 'pending') {
            return false;
        }

        return $user->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'FINANCE'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, Payment $payment): bool
    {
        if ($payment->status === 'paid') {
            return false;
        }

        if ($user->hasRole(['SUPERADMIN','ADMIN_PUSAT'])) {
            return true;
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            return $user->branch_id === $payment->branch_id;
        }

        return false;
    }
}