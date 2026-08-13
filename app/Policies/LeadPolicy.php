<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    /* =====================================================
     | VIEW
     ===================================================== */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Lead $lead): bool
    {
        return $this->ownsLead($user, $lead);
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create(User $user): bool
    {
        return $user->isActive()
            && ($user->isPusat() || $user->isCabang() || $user->isAgent());
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(User $user, Lead $lead): bool
    {
        if ($this->isFinal($lead) || $this->isLockedByPipeline($lead)) {
            return false;
        }

        return $this->ownsLead($user, $lead);
    }


    /* =====================================================
     | ASSIGN AGENT
     ===================================================== */
    // public function assignAgent(User $user, Lead $lead): bool
    // {
    //     if ($this->isLocked($lead)) {
    //         return false;
    //     }

    //     return $user->isPusat()
    //         || ($user->isCabang() && (int) $lead->branch_id === (int) $user->branch_id);
    // }

    /* =====================================================
     | MOVE PIPELINE (KANBAN / FORM)
     ===================================================== */
    public function move(User $user, Lead $lead): bool
    {
        if ($this->isFinal($lead) || $this->isLockedByPipeline($lead)) {
            return false;
        }

        return $this->ownsLead($user, $lead);
    }


    /* =====================================================
     | SUBMIT CLOSING
     ===================================================== */
    public function submitClosing(User $user, Lead $lead): bool
    {
        if ($this->isFinal($lead) || $this->isLockedByPipeline($lead)) {
            return false;
        }

        if ($user->isAgent()) {
            return (int) $lead->agent_id === (int) optional($user->agent)->id;
        }

        return $user->isCabang() || $user->isPusat();
    }
    /* =====================================================
     | INTERNAL HELPERS
     ===================================================== */

    private function ownsLead(User $user, Lead $lead): bool
    {
        // 🔓 PUSAT bebas
        if ($user->isPusat()) {
            return true;
        }

        // 🏢 CABANG
        if ($user->isCabang()) {
            return (int) $lead->branch_id === (int) $user->branch_id;
        }

        // 👤 AGENT
        if ($user->isAgent()) {
            return $lead->agent_id !== null
                && (int) $lead->agent_id === (int) optional($user->agent)->id;
        }

        return false;
    }

    /**
     * 🔒 FINAL LOCK RULE
     * - pipeline closing / lost
     * - status CLOSED
     */
    private function isFinal(Lead $lead): bool
    {
        return in_array($lead->status, ['CLOSED', 'DROPPED'], true);
    }

    private function isLockedByPipeline(Lead $lead): bool
    {
        return in_array(
            optional($lead->pipeline)->tahap,
            ['closing', 'lost'],
            true
        );
    }

    /* =====================================================
    | CREATE FOLLOW UP
    ===================================================== */
public function createFollowUp(User $user, Lead $lead): bool
{
    // ❌ lead sudah final
    if ($this->isFinal($lead)) {
        return false;
    }

    // ✅ SUPERADMIN
    if ($user->role === 'SUPERADMIN') {
        return true;
    }

    // ✅ SALES PUSAT (CRM)
    if ($user->role === 'SALES' && $user->isPusat()) {
        return true;
    }

    // ✅ AGENT → hanya lead miliknya
    if ($user->isAgent()) {
        return (int) $lead->agent_id === (int) optional($user->agent)->id;
    }

    return false;
}


}

// namespace App\Policies;

// use App\Models\Lead;
// use App\Models\User;

// class LeadPolicy
// {
//     /* =====================================================
//      | VIEW
//      ===================================================== */
//     public function viewAny(User $user): bool
//     {
//         return $user->isActive();
//     }

//     public function view(User $user, Lead $lead): bool
//     {
//         return $this->ownsLead($user, $lead);
//     }

//     /* =====================================================
//      | CREATE
//      ===================================================== */
//     public function create(User $user): bool
//     {
//         return $user->isActive()
//             && ($user->isPusat() || $user->isCabang() || $user->isAgent());
//     }

//     /* =====================================================
//      | UPDATE
//      ===================================================== */
//     public function update(User $user, Lead $lead): bool
//     {
//         if ($this->isFinal($lead)) {
//             return false;
//         }

//         return $this->ownsLead($user, $lead);
//     }

//     /* =====================================================
//      | ASSIGN AGENT
//      ===================================================== */
//     public function assignAgent(User $user, Lead $lead): bool
//     {
//         if ($this->isFinal($lead)) {
//             return false;
//         }

//         return $user->isPusat()
//             || ($user->isCabang() && $lead->branch_id === $user->branch_id);
//     }

//     /* =====================================================
//      | MOVE PIPELINE (KANBAN / FORM)
//      ===================================================== */
//     public function move(User $user, Lead $lead): bool
//     {
//         if ($this->isFinal($lead)) {
//             return false;
//         }

//         return $this->ownsLead($user, $lead);
//     }

//     /* =====================================================
//      | SUBMIT CLOSING
//      ===================================================== */
//     public function submitClosing(User $user, Lead $lead): bool
//     {
//         if ($lead->status === 'CLOSED') {
//             return false;
//         }

//         if ($user->isAgent()) {
//             return (int) $lead->agent_id === (int) optional($user->agent)->id;
//         }

//         return $user->isCabang() || $user->isPusat();
//     }
//     /* =====================================================
//      | DROP LEAD
//      ===================================================== */
//     public function drop(User $user, Lead $lead): bool
//     {
//         if ($this->isFinal($lead)) {
//             return false;
//         }

//         return $this->ownsLead($user, $lead);
//     }

//     /* =====================================================
//      | INTERNAL HELPERS
//      ===================================================== */
//     private function ownsLead(User $user, Lead $lead): bool
//     {
//         // 🔓 PUSAT bebas
//         if ($user->isPusat()) {
//             return true;
//         }

//         // 🏢 CABANG
//         if ($user->isCabang()) {
//             return (int) $lead->branch_id === (int) $user->branch_id;
//         }

//         // 👤 AGENT
//         if ($user->isAgent()) {
//             return $lead->agent_id !== null
//                 && (int) $lead->agent_id === (int) optional($user->agent)->id;
//         }

//         return false;
//     }

//     private function isFinal(Lead $lead): bool
//     {
//         return in_array($lead->status, ['CLOSED', 'DROPPED'], true);
//     }
// }
