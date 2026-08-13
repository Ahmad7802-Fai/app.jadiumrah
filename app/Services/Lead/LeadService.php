<?php

namespace App\Services\Lead;

use App\Models\Lead;
use App\Models\User;
use App\Models\Pipeline;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /* =====================================================
     | LIST / PAGINATE
     ===================================================== */
    public function paginate(Request $request)
    {
        $query = Lead::query()
            ->with([
                'source',
                'agent',
                'closing',
                'latestFollowUp',
            ]);

        /* SEARCH */
        if ($q = $request->q) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('no_hp', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        /* STATUS */
        if ($status = $request->status) {
            $query->where('leads.status', $status);
        }

        /* JOIN LAST FOLLOW UP */
        $query->leftJoin('lead_activities as a', function ($join) {
            $join->on('a.lead_id', '=', 'leads.id')
                ->whereRaw('a.id = (
                    SELECT id FROM lead_activities
                    WHERE lead_activities.lead_id = leads.id
                      AND lead_activities.deleted_at IS NULL
                    ORDER BY created_at DESC
                    LIMIT 1
                )');
        });

        /* PRIORITY SORT */
        $query->orderByRaw("
            CASE
                WHEN a.followup_date IS NOT NULL
                 AND a.followup_date < NOW()
                 AND leads.status != 'CLOSED'
                THEN 0
                ELSE 1
            END
        ");

        return $query
            ->select('leads.*')
            ->latest('leads.created_at')
            ->paginate(20)
            ->withQueryString();
    }

    /* =====================================================
     | CREATE LEAD (FULL)
     ===================================================== */
    public function create(array $data, User $user): Lead
    {
        return DB::transaction(function () use ($data, $user) {

            // OWNERSHIP
            if ($user->isAgent()) {
                $data['agent_id']  = $user->agent_id;
                $data['branch_id'] = $user->branch_id;
            } elseif ($user->isCabang()) {
                $data['branch_id'] = $user->branch_id;
            }

            // DEFAULT STATE
            $data['created_by']  = $user->id;
            $data['status']      = 'NEW';
            $data['pipeline_id'] = Pipeline::where('tahap', 'new')
                ->where('aktif', 1)
                ->value('id');

            return Lead::create($data);
        });
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Lead $lead, array $data, User $user): Lead
    {
        $this->guardEditable($lead, $user);

        $lead->update($data);
        return $lead;
    }

    /* =====================================================
     | MARK DROPPED
     ===================================================== */
    public function markDropped(
        Lead $lead,
        User $user,
        ?string $reason = null
    ): Lead {
        $this->guardEditable($lead, $user);

        $lead->update([
            'status'      => 'DROPPED',
            'drop_reason' => $reason,
            'dropped_at'  => now(),
        ]);

        return $lead;
    }

    /* =====================================================
     | DELETE (SUPERADMIN ONLY)
     ===================================================== */
    public function delete(Lead $lead, User $user): void
    {
        if (!$user->hasRole('SUPERADMIN')) {
            throw new AuthorizationException(
                'Hanya superadmin yang boleh menghapus lead.'
            );
        }

        $lead->delete();
    }

    /* =====================================================
     | INTERNAL GUARD
     ===================================================== */
    private function guardEditable(Lead $lead, User $user): void
    {
        if (
            in_array($lead->status, ['CLOSING', 'CLOSED'])
            && !$user->isPusat()
        ) {
            throw new AuthorizationException(
                'Lead sedang atau sudah closing.'
            );
        }
    }

    /* =====================================================
     | CREATE SIMPLE (AGENT)
     ===================================================== */
    public function createSimple(array $data, User $user): Lead
    {
        if (!$user->isAgent()) {
            throw new AuthorizationException('Bukan agent.');
        }

        return Lead::create([
            'agent_id'  => $user->agent_id,
            'branch_id' => $user->branch_id,
            'nama'      => $data['nama'],
            'no_hp'     => $data['no_hp'],
            'email'     => $data['email'] ?? null,
            'sumber_id' => $data['sumber_id'],
            'channel'   => $data['channel'],
            'catatan'   => $data['catatan'] ?? null,
            'status'    => 'NEW',
        ]);
    }
}

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\User;
// use App\Models\Pipeline;
// use Illuminate\Http\Request;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Support\Facades\DB;

// class LeadService
// {
//     /* =====================================================
//      | LIST / PAGINATE
//      | Visibility sepenuhnya via GLOBAL SCOPE di Model Lead
//      ===================================================== */
// public function paginate(Request $request)
// {
//     $query = Lead::query()
//         ->with([
//             'sumber',
//             'agent',
//             'closing',
//             'latestFollowUp', // untuk badge & UI
//         ]);

//     /* =====================================================
//     | SEARCH (Nama / No HP / Email)
//     ===================================================== */
//     if ($q = $request->q) {
//         $query->where(function ($w) use ($q) {
//             $w->where('nama', 'like', "%{$q}%")
//               ->orWhere('no_hp', 'like', "%{$q}%")
//               ->orWhere('email', 'like', "%{$q}%");
//         });
//     }

//     /* =====================================================
//     | STATUS FILTER
//     ===================================================== */
//     if ($status = $request->status) {
//         $query->where('leads.status', $status);
//     }

//     /* =====================================================
//     | JOIN: LAST FOLLOW UP SAJA (1 row per lead)
//     ===================================================== */
//     $query->leftJoin('lead_activities as a', function ($join) {
//         $join->on('a.lead_id', '=', 'leads.id')
//             ->whereRaw('a.id = (
//                 SELECT id FROM lead_activities
//                 WHERE lead_activities.lead_id = leads.id
//                   AND lead_activities.deleted_at IS NULL
//                 ORDER BY created_at DESC
//                 LIMIT 1
//             )');
//     });

//     /* =====================================================
//     | PRIORITAS SORT:
//     | 1️⃣ OVERDUE FOLLOW UP (PALING ATAS)
//     | 2️⃣ SISANYA NORMAL
//     ===================================================== */
//     $query->orderByRaw("
//         CASE
//             WHEN a.followup_date IS NOT NULL
//              AND a.followup_date < NOW()
//              AND leads.status != 'CLOSED'
//             THEN 0
//             ELSE 1
//         END
//     ");

//     /* =====================================================
//     | DEFAULT SORT
//     ===================================================== */
//     $query->select('leads.*')
//           ->latest('leads.created_at');

//     return $query->paginate(20)->withQueryString();
// }


//     /* =====================================================
//      | CREATE LEAD
//      ===================================================== */
//     public function create(array $data, User $user): Lead
//     {
//         return DB::transaction(function () use ($data, $user) {

//             // Ownership otomatis
//             if ($user->isAgent()) {
//                 $data['agent_id']  = $user->agent->id;
//                 $data['branch_id'] = $user->agent->branch_id;
//             }

//             if ($user->isCabang()) {
//                 $data['branch_id'] = $user->branch_id;
//             }

//             // 🔥 DEFAULT STATE (SINGLE SOURCE OF TRUTH)
//             $data['created_by']  = $user->id;
//             $data['status']      = 'NEW';
//             $data['pipeline_id'] = Pipeline::where('tahap', 'new')
//                 ->where('aktif', 1)
//                 ->value('id');

//             return Lead::create($data);
//         });
//     }

//     /* =====================================================
//      | UPDATE LEAD (SELAMA BELUM CLOSING)
//      ===================================================== */
//     public function update(Lead $lead, array $data, User $user): Lead
//     {
//         $this->guardEditable($lead, $user);

//         return DB::transaction(function () use ($lead, $data) {
//             $lead->update($data);
//             return $lead;
//         });
//     }

//     /* =====================================================
//      | MARK DROPPED (SOFT BUSINESS DROP)
//      ===================================================== */
//     public function markDropped(
//         Lead $lead,
//         User $user,
//         ?string $reason = null
//     ): Lead {
//         $this->guardEditable($lead, $user);

//         $lead->update([
//             'status'       => 'DROPPED',
//             'drop_reason'  => $reason,
//             'dropped_at'   => now(),
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | DELETE (HARD DELETE — SUPERADMIN ONLY)
//      ===================================================== */
//     public function delete(Lead $lead, User $user): void
//     {
//         if ($user->role !== 'SUPERADMIN') {
//             throw new AuthorizationException(
//                 'Hanya superadmin yang boleh menghapus lead.'
//             );
//         }

//         $lead->delete();
//     }

//     /* =====================================================
//      | INTERNAL GUARD
//      ===================================================== */

//     /**
//      * Lead hanya bisa diedit jika:
//      * - status bukan CLOSING / CLOSED
//      * - atau user adalah pusat
//      */
//     private function guardEditable(Lead $lead, User $user): void
//     {
//         if (
//             in_array($lead->status, ['CLOSING', 'CLOSED'])
//             && !$user->isPusat()
//         ) {
//             throw new AuthorizationException(
//                 'Lead sedang atau sudah closing.'
//             );
//         }
//     }

//     /* =====================================================
//     | CREATE LEAD — SIMPLE (UNTUK AGENT)
//     | ❌ TANPA PIPELINE
//     ===================================================== */
//     public function createSimple(array $data, int $agentId): Lead
//     {
//         return DB::transaction(function () use ($data, $agentId) {

//             return Lead::create([
//                 'agent_id'  => $agentId,
//                 'nama'      => $data['nama'],
//                 'no_hp'     => $data['no_hp'],
//                 'email'     => $data['email'] ?? null,
//                 'sumber_id' => $data['sumber_id'],
//                 'channel'   => $data['channel'],
//                 'catatan'   => $data['catatan'] ?? null,
//                 'status'    => 'NEW',
//             ]);
//         });
//     }

// }

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Support\Facades\DB;

// class LeadService
// {
//     /* =====================================================
//      | PAGINATION + FILTER
//      | (VISIBILITY DIHANDLE GLOBAL SCOPE)
//      ===================================================== */
//     public function paginate(Request $req)
//     {
//         $q = Lead::query(); // ⬅️ GLOBAL SCOPE AKTIF

//         if ($req->filled('status')) {
//             $q->where('status', $req->status);
//         }

//         if ($req->filled('pipeline_id')) {
//             $q->where('pipeline_id', $req->pipeline_id);
//         }

//         if ($req->filled('keyword')) {
//             $q->where(function ($s) use ($req) {
//                 $s->where('nama', 'like', "%{$req->keyword}%")
//                   ->orWhere('no_hp', 'like', "%{$req->keyword}%")
//                   ->orWhere('email', 'like', "%{$req->keyword}%");
//             });
//         }

//         return $q->latest()
//             ->paginate(20)
//             ->withQueryString();
//     }

//     /* =====================================================
//      | CREATE LEAD
//      ===================================================== */
//     public function create(array $data, User $user): Lead
//     {
//         return DB::transaction(function () use ($data, $user) {

//             if ($user->isAgent()) {
//                 $data['agent_id']  = $user->agent->id;
//                 $data['branch_id'] = $user->agent->branch_id;
//             }

//             if ($user->isCabang()) {
//                 $data['branch_id'] = $user->branch_id;
//             }

//             $data['created_by'] = $user->id;
//             $data['status']     = 'NEW';

//             return Lead::create($data);
//         });
//     }

//     /* =====================================================
//      | UPDATE LEAD
//      ===================================================== */
//     public function update(Lead $lead, array $data, User $user): Lead
//     {
//         $this->guardLockedLead($lead, $user);
//         $this->guardStatusTransition($lead, $data, $user);

//         return DB::transaction(function () use ($lead, $data) {
//             $lead->update($data);
//             return $lead;
//         });
//     }

//     /* =====================================================
//      | DELETE LEAD (SUPERADMIN ONLY)
//      ===================================================== */
//     public function delete(Lead $lead, User $user): void
//     {
//         if ($user->role !== 'SUPERADMIN') {
//             throw new AuthorizationException(
//                 'Hanya superadmin yang boleh menghapus lead.'
//             );
//         }

//         $lead->delete();
//     }

//     /* =====================================================
//      | APPROVAL: CLOSING → CLOSED (PUSAT)
//      | BYPASS GLOBAL SCOPE
//      ===================================================== */
//     public function approveClosing(int $leadId, User $user): Lead
//     {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa approve.');
//         }

//         $lead = Lead::withoutGlobalScope('access')->findOrFail($leadId);

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'     => 'CLOSED',
//             'closed_at' => now(),
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | REJECT CLOSING (PUSAT)
//      ===================================================== */
//     public function rejectClosing(
//         int $leadId,
//         User $user,
//         string $reason = null
//     ): Lead {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa reject.');
//         }

//         $lead = Lead::withoutGlobalScope('access')->findOrFail($leadId);

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'   => 'ACTIVE',
//             'catatan' => $reason,
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | INTERNAL GUARDS
//      ===================================================== */

//     private function guardLockedLead(Lead $lead, User $user): void
//     {
//         if (
//             in_array($lead->status, ['CLOSING', 'CLOSED', 'DROPPED'])
//             && !$user->isPusat()
//         ) {
//             throw new AuthorizationException(
//                 'Lead menunggu atau sudah diproses pusat.'
//             );
//         }
//     }

//     private function guardStatusTransition(
//         Lead $lead,
//         array $data,
//         User $user
//     ): void {
//         if (!isset($data['status'])) {
//             return;
//         }

//         if (
//             !$user->isPusat()
//             && $data['status'] === 'CLOSED'
//         ) {
//             throw new AuthorizationException(
//                 'Closing final harus melalui approval pusat.'
//             );
//         }
//     }
// }


// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Support\Facades\DB;

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Support\Facades\DB;

// class LeadService
// {
//     /* =====================================================
//      | PAGINATION + FILTER
//      | (VISIBILITY DIHANDLE GLOBAL SCOPE)
//      ===================================================== */
//     public function paginate(Request $req)
//     {
//         $q = Lead::query(); // ⬅️ GLOBAL SCOPE AKTIF

//         if ($req->filled('status')) {
//             $q->where('status', $req->status);
//         }

//         if ($req->filled('pipeline_id')) {
//             $q->where('pipeline_id', $req->pipeline_id);
//         }

//         if ($req->filled('keyword')) {
//             $q->where(function ($s) use ($req) {
//                 $s->where('nama', 'like', "%{$req->keyword}%")
//                   ->orWhere('no_hp', 'like', "%{$req->keyword}%")
//                   ->orWhere('email', 'like', "%{$req->keyword}%");
//             });
//         }

//         return $q->latest()
//             ->paginate(20)
//             ->withQueryString();
//     }

//     /* =====================================================
//      | CREATE LEAD
//      ===================================================== */
//     public function create(array $data, User $user): Lead
//     {
//         return DB::transaction(function () use ($data, $user) {

//             if ($user->isAgent()) {
//                 $data['agent_id']  = $user->agent->id;
//                 $data['branch_id'] = $user->agent->branch_id;
//             }

//             if ($user->isCabang()) {
//                 $data['branch_id'] = $user->branch_id;
//             }

//             $data['created_by'] = $user->id;
//             $data['status']     = 'NEW';

//             return Lead::create($data);
//         });
//     }

//     /* =====================================================
//      | UPDATE LEAD
//      ===================================================== */
//     public function update(Lead $lead, array $data, User $user): Lead
//     {
//         $this->guardLockedLead($lead, $user);
//         $this->guardStatusTransition($lead, $data, $user);

//         return DB::transaction(function () use ($lead, $data) {
//             $lead->update($data);
//             return $lead;
//         });
//     }

//     /* =====================================================
//      | DELETE LEAD (SUPERADMIN ONLY)
//      ===================================================== */
//     public function delete(Lead $lead, User $user): void
//     {
//         if ($user->role !== 'SUPERADMIN') {
//             throw new AuthorizationException(
//                 'Hanya superadmin yang boleh menghapus lead.'
//             );
//         }

//         $lead->delete();
//     }

//     /* =====================================================
//      | APPROVAL: CLOSING → CLOSED (PUSAT)
//      | BYPASS GLOBAL SCOPE
//      ===================================================== */
//     public function approveClosing(int $leadId, User $user): Lead
//     {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa approve.');
//         }

//         $lead = Lead::withoutGlobalScope('access')->findOrFail($leadId);

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'     => 'CLOSED',
//             'closed_at' => now(),
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | REJECT CLOSING (PUSAT)
//      ===================================================== */
//     public function rejectClosing(
//         int $leadId,
//         User $user,
//         string $reason = null
//     ): Lead {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa reject.');
//         }

//         $lead = Lead::withoutGlobalScope('access')->findOrFail($leadId);

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'   => 'ACTIVE',
//             'catatan' => $reason,
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | INTERNAL GUARDS
//      ===================================================== */

//     private function guardLockedLead(Lead $lead, User $user): void
//     {
//         if (
//             in_array($lead->status, ['CLOSING', 'CLOSED', 'DROPPED'])
//             && !$user->isPusat()
//         ) {
//             throw new AuthorizationException(
//                 'Lead menunggu atau sudah diproses pusat.'
//             );
//         }
//     }

//     private function guardStatusTransition(
//         Lead $lead,
//         array $data,
//         User $user
//     ): void {
//         if (!isset($data['status'])) {
//             return;
//         }

//         if (
//             !$user->isPusat()
//             && $data['status'] === 'CLOSED'
//         ) {
//             throw new AuthorizationException(
//                 'Closing final harus melalui approval pusat.'
//             );
//         }
//     }
// }


// class LeadService
// {
//     /* =====================================================
//      | QUERY BASED ON USER ACCESS
//      ===================================================== */
//     public function queryByUser(User $user): Builder
//     {
//         if ($user->isPusat()) {
//             return Lead::query();
//         }

//         if ($user->isCabang()) {
//             return Lead::where('branch_id', $user->branch_id);
//         }

//         // AGENT
//         return Lead::where('agent_id', optional($user->agent)->id);
//     }

//     /* =====================================================
//      | PAGINATION + FILTER
//      ===================================================== */
//     public function paginate(Request $req, User $user)
//     {
//         $q = $this->queryByUser($user);

//         if ($req->filled('status')) {
//             $q->where('status', $req->status);
//         }

//         if ($req->filled('pipeline_id')) {
//             $q->where('pipeline_id', $req->pipeline_id);
//         }

//         if ($req->filled('keyword')) {
//             $q->where(function ($s) use ($req) {
//                 $s->where('nama', 'like', "%{$req->keyword}%")
//                   ->orWhere('no_hp', 'like', "%{$req->keyword}%")
//                   ->orWhere('email', 'like', "%{$req->keyword}%");
//             });
//         }

//         return $q->latest()->paginate(20)->withQueryString();
//     }

//     /* =====================================================
//      | CREATE LEAD
//      ===================================================== */
//     public function create(array $data, User $user): Lead
//     {
//         return DB::transaction(function () use ($data, $user) {

//             // DEFAULT OWNERSHIP
//             if ($user->isAgent()) {
//                 $data['agent_id']  = $user->agent->id;
//                 $data['branch_id'] = $user->agent->branch_id;
//             }

//             if ($user->isCabang()) {
//                 $data['branch_id'] = $user->branch_id;
//             }

//             $data['created_by'] = $user->id;
//             $data['status']     = 'NEW';

//             return Lead::create($data);
//         });
//     }

//     /* =====================================================
//      | UPDATE LEAD
//      ===================================================== */
//     public function update(Lead $lead, array $data, User $user): Lead
//     {
//         $this->authorizeAccess($lead, $user);
//         $this->guardLockedLead($lead, $user);
//         $this->guardStatusTransition($lead, $data, $user);

//         return DB::transaction(function () use ($lead, $data) {
//             $lead->update($data);
//             return $lead;
//         });
//     }

//     /* =====================================================
//      | DELETE LEAD (SUPERADMIN ONLY)
//      ===================================================== */
//     public function delete(Lead $lead, User $user): void
//     {
//         if (!$user->role === 'SUPERADMIN') {
//             throw new AuthorizationException('Tidak diizinkan menghapus lead.');
//         }

//         $lead->delete();
//     }

//     /* =====================================================
//      | APPROVAL: CLOSING → CLOSED (PUSAT)
//      ===================================================== */
//     public function approveClosing(Lead $lead, User $user): Lead
//     {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa approve.');
//         }

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'    => 'CLOSED',
//             'closed_at'=> now(),
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | REJECT CLOSING (PUSAT)
//      ===================================================== */
//     public function rejectClosing(
//         Lead $lead,
//         User $user,
//         string $reason = null
//     ): Lead {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa reject.');
//         }

//         if ($lead->status !== 'CLOSING') {
//             throw new AuthorizationException('Lead tidak dalam status closing.');
//         }

//         $lead->update([
//             'status'   => 'ACTIVE',
//             'catatan' => $reason,
//         ]);

//         return $lead;
//     }

//     /* =====================================================
//      | INTERNAL GUARDS
//      ===================================================== */

//     private function authorizeAccess(Lead $lead, User $user): void
//     {
//         if ($user->isPusat()) {
//             return;
//         }

//         if ($user->isCabang() && $lead->branch_id === $user->branch_id) {
//             return;
//         }

//         if ($user->isAgent() && $lead->agent_id === optional($user->agent)->id) {
//             return;
//         }

//         throw new AuthorizationException('Akses lead ditolak.');
//     }

//     private function guardLockedLead(Lead $lead, User $user): void
//     {
//         if (
//             in_array($lead->status, ['CLOSING', 'CLOSED', 'DROPPED'])
//             && !$user->isPusat()
//         ) {
//             throw new AuthorizationException(
//                 'Lead menunggu atau sudah diproses pusat.'
//             );
//         }
//     }

//     private function guardStatusTransition(
//         Lead $lead,
//         array $data,
//         User $user
//     ): void {
//         if (!isset($data['status'])) {
//             return;
//         }

//         // Cabang & Agent tidak boleh CLOSED
//         if (
//             !$user->isPusat()
//             && $data['status'] === 'CLOSED'
//         ) {
//             throw new AuthorizationException(
//                 'Closing final harus melalui approval pusat.'
//             );
//         }
//     }
// }

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;

// class LeadService
// {
//     /* =====================================================
//      | CREATE LEAD
//      | - status: NEW
//      | - pipeline: tahap pertama (urutan terkecil & aktif)
//      ===================================================== */
//     public function create(array $data): Lead
//     {
//         return DB::transaction(function () use ($data) {

//             $pipelineAwal = Pipeline::where('aktif', 1)
//                 ->orderBy('urutan', 'asc')
//                 ->first();

//             if (!$pipelineAwal) {
//                 throw ValidationException::withMessages([
//                     'pipeline' => 'Pipeline awal belum dikonfigurasi.'
//                 ]);
//             }

//             return Lead::create([
//                 'nama'        => $data['nama'],
//                 'no_hp'       => $data['no_hp'],
//                 'email'       => $data['email'] ?? null,

//                 'sumber_id'   => $data['sumber_id'],
//                 'channel'     => $data['channel'], // offline | online

//                 'branch_id'   => $data['branch_id'] ?? auth()->user()->branch_id,
//                 'agent_id'    => $data['agent_id'] ?? null,

//                 'pipeline_id' => $pipelineAwal->id,
//                 'status'      => 'NEW',

//                 'catatan'     => $data['catatan'] ?? null,
//                 'created_by'  => auth()->id(),
//             ]);
//         });
//     }

//     /* =====================================================
//      | ASSIGN AGENT
//      ===================================================== */
//     public function assignAgent(Lead $lead, int $agentId): void
//     {
//         $this->guardNotClosed($lead);

//         $lead->update([
//             'agent_id' => $agentId,
//             'status'   => 'ASSIGNED',
//         ]);
//     }

//     /* =====================================================
//      | MOVE PIPELINE (KANBAN)
//      ===================================================== */
//     public function movePipeline(Lead $lead, int $pipelineId): void
//     {
//         $this->guardNotClosed($lead);

//         $pipeline = Pipeline::where('aktif', 1)->find($pipelineId);

//         if (!$pipeline) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Pipeline tidak valid atau tidak aktif.'
//             ]);
//         }

//         $lead->update([
//             'pipeline_id' => $pipeline->id,
//             'status'      => 'ACTIVE',
//         ]);
//     }

//     /* =====================================================
//      | MARK ACTIVE (saat sudah dikontak)
//      ===================================================== */
//     public function markActive(Lead $lead): void
//     {
//         $this->guardNotClosed($lead);

//         $lead->update([
//             'status' => 'ACTIVE',
//         ]);
//     }

//     /* =====================================================
//      | DROP LEAD
//      ===================================================== */
//     public function drop(Lead $lead, ?string $reason = null): void
//     {
//         $this->guardNotClosed($lead);

//         $lead->update([
//             'status'       => 'DROPPED',
//             'dropped_at'   => now(),
//             'drop_reason'  => $reason,
//         ]);
//     }

//     /* =====================================================
//      | CAN CLOSE? (GUARD UNTUK ClosingService)
//      ===================================================== */
//     public function canClose(Lead $lead): bool
//     {
//         if ($lead->status === 'CLOSED') {
//             return false;
//         }

//         // pipeline terakhir (urutan paling besar & aktif)
//         $lastPipelineId = Pipeline::where('aktif', 1)
//             ->orderBy('urutan', 'desc')
//             ->value('id');

//         return (int) $lead->pipeline_id === (int) $lastPipelineId;
//     }

//     /* =====================================================
//      | INTERNAL GUARD
//      ===================================================== */
//     private function guardNotClosed(Lead $lead): void
//     {
//         if ($lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah di-closing.'
//             ]);
//         }
//     }

//         /* =====================================================
//     | PAGINATE LEADS (UNTUK CRM INDEX)
//     ===================================================== */
//     public function paginate($req)
//     {
//         return Lead::with([
//                 'pipeline',
//                 'source',
//                 'agent',
//                 'branch',
//             ])
//             ->when($req->pipeline_id, fn ($q) =>
//                 $q->where('pipeline_id', $req->pipeline_id)
//             )
//             ->when($req->status, fn ($q) =>
//                 $q->where('status', $req->status)
//             )
//             ->when($req->keyword, fn ($q) =>
//                 $q->where('nama', 'like', "%{$req->keyword}%")
//                 ->orWhere('no_hp', 'like', "%{$req->keyword}%")
//             )
//             ->latest()
//             ->paginate(20)
//             ->withQueryString();
//     }

// }
