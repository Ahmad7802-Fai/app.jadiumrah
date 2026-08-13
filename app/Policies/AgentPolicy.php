<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Agent;

class AgentPolicy
{
    /**
     * SUPERADMIN bypass semua policy
     */
    public function before(User $user)
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }
    }

    /**
     * List agent
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['OPERATOR', 'CABANG']);
    }

    /**
     * Lihat detail agent
     */
    public function view(User $user, Agent $agent): bool
    {
        if ($user->hasRole('OPERATOR')) {
            return true;
        }

        if ($user->hasRole('CABANG')) {
            return $user->branch_id === $agent->branch_id;
        }

        return false;
    }

    /**
     * Buat agent
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['OPERATOR', 'CABANG']);
    }

    /**
     * Update agent
     */
    public function update(User $user, Agent $agent): bool
    {
        if ($user->hasRole('OPERATOR')) {
            return true;
        }

        if ($user->hasRole('CABANG')) {
            return $user->branch_id === $agent->branch_id;
        }

        return false;
    }

    /**
     * Delete agent
     * ❗ hanya OPERATOR / SUPERADMIN
     */
    public function delete(User $user, Agent $agent): bool
    {
        return $user->hasRole('OPERATOR');
    }

    /**
     * Toggle aktif/nonaktif
     */
    public function toggle(User $user, Agent $agent): bool
    {
        return $this->update($user, $agent);
    }
}

// namespace App\Policies;

// use App\Models\User;
// use App\Models\Agent;

// class AgentPolicy
// {
//     /**
//      * SUPERADMIN bypass semua policy
//      */
//     public function before(User $user)
//     {
//         if ($user->role === 'SUPERADMIN') {
//             return true;
//         }
//     }

//     /**
//      * Siapa boleh buka halaman list agent
//      */
//     public function viewAny(User $user): bool
//     {
//         return in_array($user->role, [
//             'OPERATOR',
//             'ADMIN',
//         ]);
//     }

//     /**
//      * Lihat detail agent
//      */
//     public function view(User $user, Agent $agent): bool
//     {
//         if ($user->role === 'OPERATOR') {
//             return true; // operator pusat
//         }

//         if ($user->role === 'ADMIN') {
//             return $user->branch_id === $agent->branch_id;
//         }

//         return false;
//     }

//     /**
//      * Buat agent
//      */
//     public function create(User $user): bool
//     {
//         return in_array($user->role, [
//             'OPERATOR',
//             'ADMIN',
//         ]);
//     }

//     /**
//      * Update agent
//      */
//     public function update(User $user, Agent $agent): bool
//     {
//         if ($user->role === 'OPERATOR') {
//             return true;
//         }

//         if ($user->role === 'ADMIN') {
//             return $user->branch_id === $agent->branch_id;
//         }

//         return false;
//     }

//     /**
//      * Delete agent
//      */
//     public function delete(User $user, Agent $agent): bool
//     {
//         return $user->role === 'OPERATOR';
//     }

//     /**
//      * Toggle aktif/nonaktif
//      */
//     public function toggle(User $user, Agent $agent): bool
//     {
//         return $this->update($user, $agent);
//     }
// }
