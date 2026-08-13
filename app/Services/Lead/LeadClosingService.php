<?php
namespace App\Services\Lead;

use App\Models\Lead;
use App\Models\LeadClosing;
use App\Models\Pipeline;
use App\Services\JamaahService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeadClosingService
{
    public function __construct(
        protected JamaahService $jamaahService
    ) {}

    /**
     * Submit closing (oleh sales / agent)
     */
    public function submit(Lead $lead): void
    {
        if ($lead->pipeline?->tahap !== 'komit') {
            throw ValidationException::withMessages([
                'pipeline' => 'Lead belum siap diajukan closing.'
            ]);
        }

        if ($lead->closing) {
            throw ValidationException::withMessages([
                'closing' => 'Closing sudah diajukan.'
            ]);
        }

        DB::transaction(function () use ($lead) {

            LeadClosing::create([
                'lead_id' => $lead->id,
                'status'  => 'DRAFT',
            ]);

            $lead->update([
                'status' => 'CLOSING',
            ]);
        });
    }

    /**
     * Approve closing (oleh admin / supervisor)
     */
    public function approve(Lead $lead, array $data): void
{
    DB::transaction(function () use ($lead, $data) {

        // 🔒 Lock lead
        $lead = Lead::lockForUpdate()->findOrFail($lead->id);

        // 🔒 Lock closing
        $closing = $lead->closing()->lockForUpdate()->first();

        if (!$closing || $closing->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'closing' => 'Closing tidak valid atau sudah diproses.',
            ]);
        }

        // ⛔ HARD GUARD: jangan sampai dobel
        if ($closing->jamaah_id) {
            throw ValidationException::withMessages([
                'closing' => 'Closing sudah memiliki jamaah.',
            ]);
        }

        // ⛔ HARD GUARD: jamaah sudah ada dari lead
        if (\App\Models\Jamaah::where('lead_id', $lead->id)->exists()) {
            throw ValidationException::withMessages([
                'closing' => 'Jamaah untuk lead ini sudah pernah dibuat.',
            ]);
        }

        // 1️⃣ CREATE JAMAAH (SINGLE SOURCE OF TRUTH = LEAD)
        $jamaah = $this->jamaahService->createFromClosing([
            'lead_id'      => $lead->id,              // ✅ PENTING
            'nama_lengkap' => $lead->nama,
            'no_hp'        => $lead->no_hp,
            'branch_id'    => $lead->branch_id,       // ✅ DARI LEAD
            'agent_id'     => $lead->agent_id,        // ✅ DARI LEAD
        ]);

        // 2️⃣ UPDATE CLOSING
        $closing->update([
            'jamaah_id'   => $jamaah->id,
            'nominal_dp'  => $data['nominal_dp'] ?? null,
            'total_paket' => $data['total_paket'],
            'status'      => 'APPROVED',
            'closed_at'   => now(),
        ]);

        // 3️⃣ UPDATE LEAD (FINAL STATE)
        $pipelineClosing = Pipeline::whereIn('tahap', ['closing', 'closed'])
            ->orderBy('urutan')
            ->first();

        $lead->update([
            'pipeline_id' => $pipelineClosing?->id,
            'status'      => 'CLOSED',
        ]);
    });
}

    // public function approve(Lead $lead, array $data): void
    // {
    //     DB::transaction(function () use ($lead, $data) {

    //         // 🔒 Lock lead
    //         $lead = Lead::lockForUpdate()->find($lead->id);

    //         $closing = $lead->closing()->lockForUpdate()->first();

    //         if (!$closing || $closing->status !== 'DRAFT') {
    //             throw ValidationException::withMessages([
    //                 'closing' => 'Closing tidak valid atau sudah diproses.',
    //             ]);
    //         }

    //         if ($closing->jamaah_id) {
    //             throw ValidationException::withMessages([
    //                 'closing' => 'Closing sudah memiliki jamaah.',
    //             ]);
    //         }

    //         // 1️⃣ Buat Jamaah
    //         $jamaah = $this->jamaahService->createFromClosing([
    //             'nama_lengkap' => $lead->nama,
    //             'no_hp'        => $lead->no_hp,
    //             'branch_id'    => $data['branch_id'] ?? null,
    //             'agent_id'     => $data['agent_id'] ?? null,
    //         ]);

    //         // 2️⃣ Update Closing
    //         $closing->update([
    //             'jamaah_id'   => $jamaah->id,
    //             'nominal_dp'  => $data['nominal_dp'],
    //             'total_paket' => $data['total_paket'],
    //             'status'      => 'APPROVED',
    //             'closed_at'   => now(),
    //         ]);

    //         // 3️⃣ Update Lead (SINGLE SOURCE OF TRUTH)
    //         $pipelineClosing = Pipeline::whereIn('tahap', ['closing','closed'])->first();

    //         $lead->update([
    //             'pipeline_id' => $pipelineClosing?->id,
    //             'status'      => 'CLOSED',
    //         ]);
    //     });
    // }


    /**
     * Reject closing
     */
    public function reject(LeadClosing $closing, string $reason = null): void
    {
        if ($closing->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'closing' => 'Closing sudah diproses.'
            ]);
        }

        DB::transaction(function () use ($closing, $reason) {

            $closing->update([
                'status'   => 'REJECTED',
                'catatan' => $reason,
            ]);

            $closing->lead->update([
                'status' => 'ACTIVE',
            ]);
        });
    }
}

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\LeadClosing;
// use App\Models\Jamaah;
// use App\Services\Jamaah\JamaahService;
// use App\Models\Pipeline;
// use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\DB;

// class LeadClosingService
// {
//         public function __construct(
//         protected JamaahService $jamaahService
//     ) {}
//     /**
//      * Submit closing (oleh sales / agent)
//      */
//     public function submit(Lead $lead): void
//     {
//         // ❌ pipeline belum komit
//         if ($lead->pipeline?->tahap !== 'komit') {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Lead belum siap diajukan closing.'
//             ]);
//         }

//         // ❌ sudah pernah submit
//         if ($lead->closing) {
//             throw ValidationException::withMessages([
//                 'closing' => 'Closing sudah diajukan.'
//             ]);
//         }

//         DB::transaction(function () use ($lead) {

//             // 1️⃣ create closing (PAKAI ENUM YANG ADA)
//             LeadClosing::create([
//                 'lead_id' => $lead->id,
//                 'status'  => 'DRAFT', // ✅ FIX DI SINI
//             ]);

//             // 2️⃣ update status lead
//             $lead->update([
//                 'status' => 'CLOSING',
//             ]);
//         });
//     }

//     /**
//      * Approve closing (oleh admin / supervisor)
//      */
//     public function approve(Lead $lead, array $data): void
//     {
//         $closing = $lead->closing;

//         if (!$closing || $closing->status !== 'DRAFT') {
//             throw ValidationException::withMessages([
//                 'closing' => 'Closing tidak valid atau sudah diproses.',
//             ]);
//         }

//         DB::transaction(function () use ($lead, $closing, $data) {

//             // ✅ 1️⃣ BUAT JAMAAH VIA SERVICE
//             $jamaah = $this->jamaahService->createFromClosing([
//                 'nama_lengkap' => $lead->nama,
//                 'no_hp'        => $lead->no_hp,
//                 'branch_id'    => $data['branch_id'] ?? null,
//                 'agent_id'     => $data['agent_id'] ?? null,
//             ]);

//             // ✅ 2️⃣ UPDATE CLOSING
//             $closing->update([
//                 'jamaah_id'   => $jamaah->id,
//                 'nominal_dp'  => $data['nominal_dp'],
//                 'total_paket' => $data['total_paket'],
//                 'status'      => 'APPROVED',
//                 'closed_at'   => now(),
//             ]);

//             // ✅ 3️⃣ UPDATE LEAD
//             $pipelineClosing = Pipeline::where('tahap', 'closing')->first();

//             $lead->update([
//                 'pipeline_id' => $pipelineClosing?->id,
//                 'status'      => 'CLOSED',
//             ]);
//         });
//     }
//     /**
//      * Reject closing
//      */
//     public function reject(LeadClosing $closing, string $reason = null): void
//     {
//         if ($closing->status !== 'PENDING') {
//             throw ValidationException::withMessages([
//                 'closing' => 'Closing sudah diproses.'
//             ]);
//         }

//         DB::transaction(function () use ($closing, $reason) {

//             $closing->update([
//                 'status' => 'REJECTED',
//                 'note'   => $reason,
//             ]);

//             // balikin ke ACTIVE
//             $closing->lead->update([
//                 'status' => 'ACTIVE',
//             ]);
//         });
//     }
// }

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\LeadClosing;
// use App\Notifications\LeadClosingApproved;
// use App\Notifications\LeadClosingRejected;
// use App\Models\User;
// use Illuminate\Auth\Access\AuthorizationException;
// use Illuminate\Support\Facades\DB;

// class LeadClosingService
// {
//     /* =====================================================
//      | SUBMIT CLOSING
//      | Agent / Cabang
//      ===================================================== */
//     public function submitClosing(
//         Lead $lead,
//         array $data,
//         User $user
//     ): LeadClosing {

//         // Hanya agent / cabang / pusat
//         if (!$user->isAgent() && !$user->isCabang() && !$user->isPusat()) {
//             throw new AuthorizationException('Tidak diizinkan mengajukan closing.');
//         }

//         // Lead harus editable
//         if (in_array($lead->status, ['CLOSING', 'CLOSED'])) {
//             throw new AuthorizationException('Lead sudah atau sedang closing.');
//         }

//         return DB::transaction(function () use ($lead, $data, $user) {

//             // Buat record closing
//             $closing = LeadClosing::create([
//                 'lead_id'     => $lead->id,
//                 'jamaah_id'   => $data['jamaah_id'] ?? null,
//                 'agent_id'    => $lead->agent_id,
//                 'branch_id'   => $lead->branch_id,
//                 'nominal_dp'  => $data['nominal_dp'] ?? null,
//                 'total_paket' => $data['total_paket'] ?? null,
//                 'status'      => 'DRAFT',
//                 'catatan'     => $data['catatan'] ?? null,
//             ]);

//             // Update state lead
//             $lead->update([
//                 'status' => 'CLOSING',
//             ]);

//             return $closing;
//         });
//     }

//     /* =====================================================
//      | APPROVE CLOSING
//      | PUSAT ONLY
//      ===================================================== */
//     public function approve(int $closingId, User $user): LeadClosing
//     {
//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa approve.');
//         }

//         return DB::transaction(function () use ($closingId) {

//             $closing = LeadClosing::lockForUpdate()
//                 ->with(['lead', 'agent', 'branch'])
//                 ->findOrFail($closingId);

//             if ($closing->status !== LeadClosing::STATUS_DRAFT) {
//                 throw new AuthorizationException('Closing sudah diproses.');
//             }

//             $closing->update([
//                 'status'    => LeadClosing::STATUS_APPROVED,
//                 'closed_at' => now(),
//             ]);

//             $closing->lead()->update([
//                 'status' => 'CLOSED',
//             ]);

//             // 🔔 NOTIFIKASI
//             $this->notifyOwner(
//                 $closing,
//                 new LeadClosingApproved($closing)
//             );

//             return $closing;
//         });
//     }


//     /* =====================================================
//      | REJECT CLOSING
//      | PUSAT ONLY
//      ===================================================== */
//     public function reject(
//         int $closingId,
//         User $user,
//         ?string $reason = null
//     ): LeadClosing {

//         if (!$user->isPusat()) {
//             throw new AuthorizationException('Hanya pusat yang bisa reject.');
//         }

//         return DB::transaction(function () use ($closingId, $reason) {

//             $closing = LeadClosing::lockForUpdate()
//                 ->with(['lead', 'agent', 'branch'])
//                 ->findOrFail($closingId);

//             if ($closing->status !== LeadClosing::STATUS_DRAFT) {
//                 throw new AuthorizationException('Closing sudah diproses.');
//             }

//             $closing->update([
//                 'status'  => LeadClosing::STATUS_REJECTED,
//                 'catatan'=> $reason,
//             ]);

//             $closing->lead()->update([
//                 'status' => 'ACTIVE',
//             ]);

//             // 🔔 NOTIFIKASI
//             $this->notifyOwner(
//                 $closing,
//                 new LeadClosingRejected($closing)
//             );

//             return $closing;
//         });
//     }
//     // Notifikasi
//     private function notifyOwner(
//         LeadClosing $closing,
//         $notification
//     ): void {

//         // AGENT
//         if ($closing->agent_id) {
//             User::whereHas('agent', function ($q) use ($closing) {
//                 $q->where('id', $closing->agent_id);
//             })->each->notify($notification);
//         }

//         // CABANG
//         if ($closing->branch_id) {
//             User::where('branch_id', $closing->branch_id)
//                 ->where('role', 'ADMIN')
//                 ->each->notify($notification);
//         }
//     }


//     /* =====================================================
//      | LIST PENDING CLOSING (PUSAT)
//      | Approval Queue
//      ===================================================== */
//     public function pendingForApproval()
//     {
//         return LeadClosing::where('status', 'DRAFT')
//             ->with(['lead', 'lead.agent', 'lead.branch'])
//             ->latest()
//             ->paginate(20);
//     }
// }
