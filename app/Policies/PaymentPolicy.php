<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payments;
use App\Models\Jamaah;

class PaymentPolicy
{
    /**
     * INPUT PEMBAYARAN
     * - ADMIN CABANG
     * - SALES / AGENT
     * - SUPERADMIN
     *
     * ❌ Jamaah TABUNGAN tidak boleh input payment
     */
    public function create(User $user, Jamaah $jamaah): bool
    {
        // 🚫 BLOKIR JAMAah TABUNGAN
        if ($jamaah->tipe_jamaah === 'tabungan') {
            return false;
        }

        // ✅ ROLE YANG BOLEH INPUT
        return in_array($user->role, [
            'ADMIN',
            'SALES',
            'SUPERADMIN',
        ], true);
    }

    /**
     * APPROVE PAYMENT
     * - KEUANGAN
     * - SUPERADMIN
     *
     * ❌ Payment dari jamaah TABUNGAN tidak boleh diproses
     */
    public function approve(User $user, Payments $payment): bool
    {
        return $this->canProcess($user, $payment);
    }

    /**
     * REJECT PAYMENT
     * - KEUANGAN
     * - SUPERADMIN
     */
    public function reject(User $user, Payments $payment): bool
    {
        return $this->canProcess($user, $payment);
    }

    /**
     * GUARD UTAMA PROSES PAYMENT
     */
    private function canProcess(User $user, Payments $payment): bool
    {
        // 🚫 BLOKIR kalau jamaah TABUNGAN
        if (
            $payment->jamaah &&
            $payment->jamaah->tipe_jamaah === 'tabungan'
        ) {
            return false;
        }

        return
            in_array($user->role, ['KEUANGAN', 'SUPERADMIN'], true)
            && $payment->status === 'pending'
            && (int) $payment->is_deleted === 0;
    }
}

// namespace App\Policies;

// use App\Models\User;
// use App\Models\Payments;

// class PaymentPolicy
// {
//     /**
//      * INPUT PEMBAYARAN
//      * - CABANG (ADMIN)
//      * - AGENT (SALES)
//      * - PUSAT (SUPERADMIN)
//      */
//     public function create(User $user): bool
//     {
//         return in_array($user->role, [
//             'ADMIN',      // CABANG
//             'SALES',      // AGENT
//             'SUPERADMIN', // PUSAT
//         ], true);
//     }

//     /**
//      * APPROVE / REJECT
//      * - KEUANGAN
//      * - SUPERADMIN
//      */
//     public function approve(User $user, Payments $payment): bool
//     {
//         return $this->canProcess($user, $payment);
//     }

//     public function reject(User $user, Payments $payment): bool
//     {
//         return $this->canProcess($user, $payment);
//     }

//     private function canProcess(User $user, Payments $payment): bool
//     {
//         return
//             in_array($user->role, ['KEUANGAN', 'SUPERADMIN'], true)
//             && $payment->status === 'pending'
//             && (int) $payment->is_deleted === 0;
//     }
// }
