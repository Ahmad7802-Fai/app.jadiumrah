<?php

namespace App\Services\Lead;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeadActivityService
{
    public function __construct(
        protected LeadPipelineService $pipelineService
    ) {}

    /* =====================================================
     | API LAMA — JANGAN DIHAPUS
     ===================================================== */
    public function log(Lead $lead, array $data): LeadActivity
    {
        return $this->store($lead, $data);
    }

    /* =====================================================
     | CREATE ACTIVITY (INTI CRM)
     ===================================================== */
    public function store(Lead $lead, array $data): LeadActivity
    {
        $user = auth()->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'auth' => 'User tidak terautentikasi.'
            ]);
        }

        // 🔐 Ownership guard (final safety net)
        $this->guardAccess($lead, $user);

        if ($lead->status === 'CLOSED') {
            throw ValidationException::withMessages([
                'lead' => 'Lead sudah closed.'
            ]);
        }

        if (empty($data['aktivitas'])) {
            throw ValidationException::withMessages([
                'aktivitas' => 'Aktivitas wajib diisi.'
            ]);
        }

        return DB::transaction(function () use ($lead, $data, $user) {

            /* ===============================
             | CREATE ACTIVITY (SOURCE OF TRUTH)
             =============================== */
            $activity = LeadActivity::create([
                'lead_id'       => $lead->id,
                'user_id'       => $user->id,
                'aktivitas'     => $data['aktivitas'],
                'hasil'         => $data['hasil'] ?? null,
                'next_action'   => $data['next_action'] ?? null,
                'followup_date' => $data['followup_date'] ?? null,
            ]);

            /* ===============================
             | PIPELINE MOVE (BEST EFFORT)
             =============================== */
            try {
                $this->pipelineService->moveByActivity(
                    $lead,
                    $data['aktivitas'],
                    $user->id
                );
            } catch (\Throwable $e) {
                report($e);
            }

            return $activity;
        });
    }

    /* =====================================================
     | UPDATE ACTIVITY
     ===================================================== */
    public function update(
        LeadActivity $activity,
        array $data
    ): LeadActivity {

        $user = auth()->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'auth' => 'User tidak terautentikasi.'
            ]);
        }

        $lead = $activity->lead;

        $this->guardAccess($lead, $user);

        if ($lead->status === 'CLOSED') {
            throw ValidationException::withMessages([
                'lead' => 'Lead sudah closed.'
            ]);
        }

        $activity->update([
            'aktivitas'     => $data['aktivitas'],
            'hasil'         => $data['hasil'] ?? null,
            'next_action'   => $data['next_action'] ?? null,
            'followup_date' => $data['followup_date'] ?? null,
        ]);

        return $activity;
    }

    /* =====================================================
     | DELETE (SOFT DELETE)
     ===================================================== */
    public function delete(LeadActivity $activity): void
    {
        $user = auth()->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'auth' => 'User tidak terautentikasi.'
            ]);
        }

        $lead = $activity->lead;

        $this->guardAccess($lead, $user);

        if ($lead->status === 'CLOSED') {
            throw ValidationException::withMessages([
                'lead' => 'Lead sudah closed.'
            ]);
        }

        $activity->delete();
    }

    /* =====================================================
     | UPCOMING FOLLOW UP
     | (GLOBAL SCOPE LEAD AKAN MEMFILTER)
     ===================================================== */
    public function upcomingFollowUps(int $days = 3)
    {
        return LeadActivity::whereNotNull('followup_date')
            ->whereBetween('followup_date', [
                now(),
                now()->addDays($days),
            ])
            ->orderBy('followup_date')
            ->get();
    }

    /* =====================================================
     | INTERNAL GUARD (FINAL)
     ===================================================== */
    private function guardAccess(Lead $lead, User $user): void
    {
        // SUPERADMIN / PUSAT
        if ($user->isPusat()) {
            return;
        }

        // AGENT → hanya lead miliknya
        if ($user->isAgent()) {
            if ((int) $lead->agent_id !== (int) $user->agent->id) {
                throw ValidationException::withMessages([
                    'lead' => 'Tidak memiliki akses ke lead ini.'
                ]);
            }
            return;
        }

        // CABANG → by branch
        if ($user->isCabang()) {
            if ((int) $lead->branch_id !== (int) $user->branch_id) {
                throw ValidationException::withMessages([
                    'lead' => 'Lead bukan milik cabang Anda.'
                ]);
            }
            return;
        }

        throw ValidationException::withMessages([
            'lead' => 'Tidak memiliki akses.'
        ]);
    }
}

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\LeadActivity;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;

// class LeadActivityService
// {
//     public function __construct(
//         protected LeadPipelineService $pipelineService
//     ) {}

//     /* =====================================================
//      | API LAMA — JANGAN DIHAPUS
//      | dipakai pusat & cabang
//      ===================================================== */
//     public function log(Lead $lead, array $data): LeadActivity
//     {
//         return $this->store($lead, $data);
//     }

//     public function store(Lead $lead, array $data): LeadActivity
//     {
//         if ($lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah closed.'
//             ]);
//         }

//         if (!isset($data['aktivitas'])) {
//             throw ValidationException::withMessages([
//                 'aktivitas' => 'Aktivitas wajib diisi.'
//             ]);
//         }

//         // ✅ DATA UTAMA (WAJIB MASUK)
//         $activity = LeadActivity::create([
//             'lead_id'       => $lead->id,
//             'user_id'       => auth()->id(),
//             'aktivitas'     => $data['aktivitas'],
//             'hasil'         => $data['hasil'] ?? null,
//             'next_action'   => $data['next_action'] ?? null,
//             'followup_date' => $data['followup_date'] ?? null,
//         ]);

//         // 🔁 EFEK SAMPING (BOLEH GAGAL)
//         try {
//             $this->pipelineService->moveByActivity(
//                 $lead,
//                 $data['aktivitas'],
//                 auth()->id()
//             );
//         } catch (\Throwable $e) {
//             report($e); // logging saja
//         }

//         return $activity;
//     }

//     /* =====================================================
//      | UPDATE ACTIVITY
//      ===================================================== */
//     public function update(
//         LeadActivity $activity,
//         array $data
//     ): LeadActivity {

//         if ($activity->lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah closed.'
//             ]);
//         }

//         $activity->update([
//             'aktivitas'     => $data['aktivitas'],
//             'hasil'         => $data['hasil'] ?? null,
//             'next_action'   => $data['next_action'] ?? null,
//             'followup_date' => $data['followup_date'] ?? null,
//         ]);

//         return $activity;
//     }

//     /* =====================================================
//      | DELETE (SOFT DELETE)
//      ===================================================== */
//     public function delete(LeadActivity $activity): void
//     {
//         if ($activity->lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah closed.'
//             ]);
//         }

//         $activity->delete();
//     }

//     /* =====================================================
//      | UPCOMING FOLLOW UP
//      ===================================================== */
//     public function upcomingFollowUps(int $days = 3)
//     {
//         return LeadActivity::whereNotNull('followup_date')
//             ->whereBetween('followup_date', [
//                 now(),
//                 now()->addDays($days),
//             ])
//             ->orderBy('followup_date')
//             ->get();
//     }
// }

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\LeadActivity;
// use App\Services\Lead\LeadPipelineService;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;

// class LeadActivityService
// { 
//     public function __construct(
//         protected LeadPipelineService $pipelineService
//     ) {}

//     /**
//      * API LAMA — JANGAN DIHAPUS
//      * dipakai pusat & cabang
//      */
//     public function log(Lead $lead, array $data): LeadActivity
//     {
//         return $this->store($lead, $data);
//     }

//     /**
//      * API BARU — dipakai agent / future
//      */
//    public function store(Lead $lead, array $data): LeadActivity
//     {
//         return DB::transaction(function () use ($lead, $data) {

//             if ($lead->status === 'CLOSED') {
//                 throw ValidationException::withMessages([
//                     'lead' => 'Lead sudah closed.'
//                 ]);
//             }

//             $activity = LeadActivity::create([
//                 'lead_id'       => $lead->id,
//                 'user_id'       => auth()->id(),
//                 'aktivitas'     => $data['aktivitas'],
//                 'hasil'         => $data['hasil'],
//                 'next_action'   => $data['next_action'] ?? null,
//                 'followup_date' => $data['followup_date'] ?? null,
//             ]);

//             // 🔥 AUTO PIPELINE — FIX FINAL
//             $this->pipelineService->moveByActivity(
//                 $lead,
//                 $data['hasil'],
//                 auth()->id()
//             );

//             return $activity;
//         });
//     }

//     public function update(
//         LeadActivity $activity,
//         array $data
//     ): LeadActivity {
//         if ($activity->lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah closed.'
//             ]);
//         }

//         $activity->update([
//             'aktivitas'     => $data['aktivitas'],
//             'hasil'         => $data['hasil'] ?? null,
//             'next_action'   => $data['next_action'] ?? null,
//             'followup_date' => $data['followup_date'] ?? null,
//         ]);

//         return $activity;
//     }

//     /* =====================================================
//      | DELETE ACTIVITY (SOFT DELETE)
//      ===================================================== */
//     public function delete(LeadActivity $activity): void
//     {
//         if ($activity->lead->status === 'CLOSED') {
//             throw ValidationException::withMessages([
//                 'lead' => 'Lead sudah closed.'
//             ]);
//         }

//         $activity->delete();
//     }

//     /* =====================================================
//      | GET UPCOMING FOLLOW UP
//      | untuk reminder dashboard
//      ===================================================== */
//     public function upcomingFollowUps(int $days = 3)
//     {
//         return LeadActivity::whereNotNull('followup_date')
//             ->whereBetween('followup_date', [
//                 now(),
//                 now()->addDays($days),
//             ])
//             ->orderBy('followup_date')
//             ->get();
//     }
// }
