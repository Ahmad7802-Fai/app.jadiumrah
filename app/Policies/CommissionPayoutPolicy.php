<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CommissionPayout;

class CommissionPayoutPolicy
{
    /*
    |--------------------------------------------------------------------------
    | VIEW ANY (List)
    |--------------------------------------------------------------------------
    */
    public function viewAny(User $user): bool
    {
        return $user->hasRole([
            'SUPERADMIN',
            'FINANCE',
            'KEUANGAN_PUSAT',
            'KEUANGAN_CABANG'
        ]) || $user->can('commission.payout.view');
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW SINGLE
    |--------------------------------------------------------------------------
    */
    public function view(User $user, CommissionPayout $payout): bool
    {
        // SUPERADMIN bisa semua
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Finance bisa semua
        if ($user->can('commission.payout.view')) {
            return true;
        }

        // Agent hanya bisa lihat payout miliknya
        if ($user->hasRole('AGENT')) {
            return $payout->agent_id === $user->agent?->id;
        }

        // Branch finance hanya bisa lihat cabangnya
        if ($user->hasRole('KEUANGAN_CABANG')) {
            return $payout->branch_id === $user->branch_id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | REQUEST PAYOUT (AGENT)
    |--------------------------------------------------------------------------
    */
    public function request(User $user): bool
    {
        return $user->hasRole('AGENT')
            && $user->can('commission.payout.request');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PAYOUT
    |--------------------------------------------------------------------------
    */
    public function approve(User $user, CommissionPayout $payout): bool
    {
        if (!$user->can('commission.payout.approve')) {
            return false;
        }

        // hanya bisa approve jika status request
        if ($payout->status !== 'request') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID
    |--------------------------------------------------------------------------
    */
    public function pay(User $user, CommissionPayout $payout): bool
    {
        if (!$user->can('commission.payout.pay')) {
            return false;
        }

        // hanya bisa mark paid jika approved
        if ($payout->status !== 'approved') {
            return false;
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE (hanya jika masih request)
    |--------------------------------------------------------------------------
    */
    public function update(User $user, CommissionPayout $payout): bool
    {
        if ($payout->status !== 'request') {
            return false;
        }

        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        if ($user->hasRole('AGENT')) {
            return $payout->agent_id === $user->agent?->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE (hanya sebelum approve)
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, CommissionPayout $payout): bool
    {
        if ($payout->status !== 'request') {
            return false;
        }

        return $user->hasRole('SUPERADMIN');
    }
}