<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Jamaah;

class JamaahPolicy
{
    /* ===============================
     | VIEW
     =============================== */
    public function view(User $user, Jamaah $jamaah): bool
    {
        // =====================
        // PUSAT
        // =====================
        if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'], true)) {
            return true;
        }

        // =====================
        // CABANG (ADMIN)
        // =====================
        if ($user->role === 'ADMIN') {
            return (int) $user->branch_id === (int) $jamaah->branch_id;
        }

        // =====================
        // AGENT / SALES
        // =====================
        if ($user->role === 'SALES') {

            // Jamaah belum punya agent → boleh lihat jika satu cabang
            if ($jamaah->agent_id === null) {
                return (int) $user->branch_id === (int) $jamaah->branch_id;
            }

            // Jamaah sudah punya agent → hanya agent pemilik
            return \App\Models\Agent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('id', $jamaah->agent_id)
                ->exists();
        }

        return false;
    }

    /* =====================================================
     | CREATE JAMAAH
     ===================================================== */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'SUPERADMIN',
            'OPERATOR',
            'ADMIN',
            'SALES',
        ], true);
    }

    /* =====================================================
     | UPDATE JAMAAH
     ===================================================== */
    public function update(User $user, Jamaah $jamaah): bool
    {
        // ❌ Tidak boleh edit jika sudah diproses
        if (in_array($jamaah->status, ['approved', 'rejected'], true)) {
            return false;
        }

        // =====================
        // PUSAT
        // =====================
        if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'], true)) {
            return true;
        }

        // =====================
        // CABANG
        // =====================
        if ($user->role === 'ADMIN') {
            return (int) $user->branch_id === (int) $jamaah->branch_id;
        }

        // =====================
        // AGENT
        // =====================
        if ($user->role === 'SALES') {
            return \App\Models\Agent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('id', $jamaah->agent_id)
                ->exists();
        }
        
        return false;
    }

    /* =====================================================
     | DELETE JAMAAH
     ===================================================== */
    public function delete(User $user, Jamaah $jamaah): bool
    {
        // hanya pusat & hanya jika belum approved
        if ($jamaah->status !== 'pending') {
            return false;
        }

        return in_array($user->role, ['SUPERADMIN', 'OPERATOR'], true);
    }

    /* =====================================================
     | APPROVAL (KHUSUS PUSAT)
     ===================================================== */
    public function approve(User $user, Jamaah $jamaah): bool
    {
        if ($jamaah->status !== 'pending') {
            return false;
        }

        return in_array($user->role, ['SUPERADMIN', 'OPERATOR'], true);
    }
}

// namespace App\Policies;

// use App\Models\User;
// use App\Models\Jamaah;

// class JamaahPolicy
// {
//     /* ===============================
//      | VIEW
//      =============================== */
//     public function view(User $user, Jamaah $jamaah): bool
//     {
//         // PUSAT
//         if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'], true)) {
//             return true;
//         }

//         // CABANG
//         if ($user->role === 'ADMIN') {
//             return (int) $user->branch_id === (int) $jamaah->branch_id;
//         }

//         // AGENT (SALES) ✅ FIX
//         if ($user->role === 'SALES') {
//             return $user->agent !== null
//                 && (int) $user->agent->id === (int) $jamaah->agent_id;
//         }

//         return false;
//     }

//     /* =====================================================
//      | CREATE JAMAAH
//      ===================================================== */
//     public function create(User $user): bool
//     {
//         return in_array($user->role, [
//             'SUPERADMIN',
//             'OPERATOR',
//             'ADMIN',
//             'SALES',
//         ]);
//     }
//     /* =====================================================
//      | UPDATE JAMAAH
//      ===================================================== */
//     public function update(User $user, Jamaah $jamaah): bool
//     {
//         // ❌ TIDAK BOLEH EDIT SETELAH DIPROSES
//         if (in_array($jamaah->status, ['approved', 'rejected'])) {
//             return false;
//         }

//         // SUPERADMIN & OPERATOR bebas (sebelum approved)
//         if (in_array($user->role, ['SUPERADMIN', 'OPERATOR'])) {
//             return true;
//         }

//         // ADMIN hanya jamaah cabangnya
//         if ($user->role === 'ADMIN') {
//             return $jamaah->branch_id === $user->branch_id;
//         }

//         // SALES hanya jamaah miliknya
//         if ($user->role === 'SALES') {
//             return $user->agent !== null
//                 && (int) $user->agent->id === (int) $jamaah->agent_id;
//         }


//         return false;
//     }

//     /* =====================================================
//      | DELETE JAMAAH
//      ===================================================== */
//     public function delete(User $user, Jamaah $jamaah): bool
//     {
//         // hanya pusat & hanya jika belum approved
//         if ($jamaah->status !== 'pending') {
//             return false;
//         }

//         return in_array($user->role, ['SUPERADMIN', 'OPERATOR']);
//     }

//     /* =====================================================
//      | APPROVAL (KHUSUS PUSAT)
//      ===================================================== */
//     public function approve(User $user, Jamaah $jamaah): bool
//     {
        
//         // hanya jamaah pending yang bisa di-approve
//         if ($jamaah->status !== 'pending') {
//             return false;
//         }

//         return in_array($user->role, [
//             'SUPERADMIN',
//             'OPERATOR',
//         ]);
//     }

// }
