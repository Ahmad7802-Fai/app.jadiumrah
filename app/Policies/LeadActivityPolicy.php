<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;

class LeadActivityPolicy
{
    /* =====================================================
     | VIEW ACTIVITY
     | - PUSAT  : semua
     | - CABANG : lead cabangnya
     | - AGENT  : lead miliknya
     ===================================================== */
    public function view(User $user, LeadActivity $activity): bool
    {
        return $this->ownsLead($user, $activity->lead);
    }

    /* =====================================================
     | CREATE ACTIVITY
     | - tidak boleh jika lead CLOSED
     | - sesuai kepemilikan lead
     ===================================================== */
    public function create(User $user, Lead $lead): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($lead->status === 'CLOSED') {
            return false;
        }

        return $this->ownsLead($user, $lead);
    }

    /* =====================================================
     | UPDATE ACTIVITY
     | - hanya DRAFT (soft-delete belum)
     | - PUSAT boleh semua
     | - lainnya hanya activity miliknya
     ===================================================== */
    public function update(User $user, LeadActivity $activity): bool
    {
        if ($activity->lead->status === 'CLOSED') {
            return false;
        }

        // 🔓 PUSAT bebas
        if ($user->isPusat()) {
            return true;
        }

        // ✍️ hanya pembuat activity
        return (int) $activity->user_id === (int) $user->id;
    }

    /* =====================================================
     | DELETE ACTIVITY (SOFT DELETE)
     | - sama dengan update
     ===================================================== */
    public function delete(User $user, LeadActivity $activity): bool
    {
        return $this->update($user, $activity);
    }

    /* =====================================================
     | VIEW ANY (LIST ACTIVITY)
     ===================================================== */
    public function viewAny(User $user): bool
    {
        return $user->isActive()
            && ($user->isPusat()
                || $user->isCabang()
                || $user->isAgent());
    }

    /* =====================================================
     | ================= HELPERS =================
     ===================================================== */

    private function ownsLead(User $user, Lead $lead): bool
    {
        if ($user->isPusat()) {
            return true;
        }

        if ($user->isCabang()) {
            return (int) $lead->branch_id === (int) $user->branch_id;
        }

        if ($user->isAgent()) {
            return (int) $lead->agent_id === (int) optional($user->agent)->id;
        }

        return false;
    }
}
