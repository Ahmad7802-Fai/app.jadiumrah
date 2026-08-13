<?php

namespace App\Services\Pipeline;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PipelineService
{
    /**
     * =====================================================
     * TRANSITION — SATU-SATUNYA GERBANG PINDAH PIPELINE
     * =====================================================
     */
    public function transition(
        Lead $lead,
        Pipeline $to,
        string $action,
        User $actor
    ): void {
        DB::transaction(function () use ($lead, $to, $action, $actor) {

            $from = $lead->pipeline;

            if ($this->isFinal($lead)) {
                throw ValidationException::withMessages([
                    'lead' => 'Lead sudah berada di status final.'
                ]);
            }

            if ($from && $from->id === $to->id) {
                throw ValidationException::withMessages([
                    'pipeline' => 'Lead sudah berada di pipeline ini.'
                ]);
            }

            // 🔁 Update lead
            $lead->update([
                'pipeline_id' => $to->id,
                'status'      => $this->resolveStatus($to),
            ]);

            // 🧾 Log
            PipelineLog::create([
                'lead_id'            => $lead->id,
                'from_pipeline_id'   => $from?->id,
                'to_pipeline_id'     => $to->id,
                'from_pipeline_name' => $from?->tahap,
                'to_pipeline_name'   => $to->tahap,
                'action'             => $action,
                'created_by'         => $actor->id,
            ]);
        });
    }
    /* =====================================================
     | RESOLVE STATUS — AUTO DARI PIPELINE
     ===================================================== */
    private function resolveStatus(Pipeline $pipeline): string
    {
        return match ($pipeline->tahap) {
            'new'       => 'NEW',
            'prospect'  => 'ACTIVE',
            'followup'  => 'ACTIVE',
            'meeting'   => 'ACTIVE',
            'komit'     => 'ACTIVE',
            'closing'   => 'ACTIVE',
            'lost'      => 'DROPPED',
            default     => 'ACTIVE',
        };
    }

    public function isLocked(Lead $lead): bool
    {
        return in_array(
            optional($lead->pipeline)->tahap,
            ['closing', 'lost'],
            true
        ) || in_array($lead->status, ['CLOSED', 'DROPPED'], true);
    }

    /* =====================================================
     | FINAL STATE
     ===================================================== */
    private function isFinal(Lead $lead): bool
    {
        return in_array($lead->status, ['CLOSED', 'DROPPED'], true);
    }
}


// namespace App\Services\Pipeline;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use App\Models\PipelineLog;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;

// class PipelineService
// {
//     /**
//      * =====================================================
//      * MOVE PIPELINE (ALL ENTRY POINT)
//      * action: DRAG | MANUAL | SUBMIT_CLOSING | APPROVE | REJECT
//      * =====================================================
//      */
//     public function move(
//         Lead $lead,
//         Pipeline $to,
//         string $action,
//         ?int $userId = null
//     ): void {
//         DB::transaction(function () use ($lead, $to, $action, $userId) {

//             $from = $lead->pipeline;

//             // 🔒 Guard: tidak boleh pindah ke pipeline yang sama
//             if ($from && (int) $from->id === (int) $to->id) {
//                 throw ValidationException::withMessages([
//                     'pipeline' => 'Lead sudah berada di pipeline ini.'
//                 ]);
//             }

//             // 🔒 Guard: pipeline final tidak boleh dipindah
//             if ($this->isLocked($lead)) {
//                 throw ValidationException::withMessages([
//                     'lead' => 'Lead sudah terkunci.'
//                 ]);
//             }

//             /* =========================
//              | 1️⃣ UPDATE LEAD PIPELINE
//              ========================= */
//             $lead->update([
//                 'pipeline_id' => $to->id,
//                 'status'      => $this->resolveStatus($to->tahap),
//             ]);

//             /* =========================
//              | 2️⃣ PIPELINE LOG
//              ========================= */
//             PipelineLog::create([
//                 'lead_id'            => $lead->id,
//                 'from_pipeline_id'   => $from?->id,
//                 'to_pipeline_id'     => $to->id,
//                 'from_pipeline_name' => $from?->tahap,
//                 'to_pipeline_name'   => $to->tahap,
//                 'action'             => $action,
//                 'created_by'         => $userId,
//             ]);
//         });
//     }

//     /* =====================================================
//      | LOCK RULE
//      ===================================================== */
//     public function isLocked(Lead $lead): bool
//     {
//         return in_array(optional($lead->pipeline)->tahap, [
//             'closing',
//             'lost',
//         ], true)
//         || $lead->status === 'CLOSED';
//     }

//     /* =====================================================
//      | STATUS RESOLVER (SINGLE SOURCE)
//      ===================================================== */
//     private function resolveStatus(string $pipelineTahap): string
//     {
//         return match ($pipelineTahap) {
//             'new'      => 'NEW',
//             'lost'     => 'DROPPED',
//             default    => 'ACTIVE',
//         };
//     }
// }


// namespace App\Services\Pipeline;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use App\Models\PipelineLog;
// use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\Auth;

// class PipelineService
// {
//     public function move(
//         Lead $lead,
//         Pipeline $to,
//         string $action = 'DRAG'
//     ): void {
//         $from = $lead->pipeline;

//         // ⛔ Guard: tidak boleh ke pipeline yang sama
//         if ($from && $from->id === $to->id) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Lead sudah berada di pipeline ini.'
//             ]);
//         }

//         // 1️⃣ UPDATE LEAD
//         $lead->update([
//             'pipeline_id' => $to->id,
//         ]);

//         // 2️⃣ INSERT PIPELINE LOG (WAJIB LENGKAP)
//         PipelineLog::create([
//             'lead_id'             => $lead->id,
//             'from_pipeline_id'    => $from?->id,
//             'to_pipeline_id'      => $to->id,
//             'from_pipeline_name'  => $from?->tahap,
//             'to_pipeline_name'    => $to->tahap,
//             'action'              => $action,
//             'created_by'          => Auth::id(),
//         ]);
//     }
// }


// app/Services/Pipeline/PipelineService.php
// namespace App\Services\Pipeline;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use App\Models\PipelineLog;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Validation\ValidationException;

// class PipelineService
// {
//     /**
//      * Pindahkan lead ke pipeline lain + log
//      */
//     public function moveToPipeline(
//         Lead $lead,
//         int $toPipelineId,
//         string $action = 'DRAG',
//         ?int $userId = null
//     ): void {
//         $fromPipeline = $lead->pipeline;
//         $toPipeline   = Pipeline::findOrFail($toPipelineId);

//         // 🚫 Guard: tidak boleh pindah ke pipeline yang sama
//         if ($fromPipeline && $fromPipeline->id === $toPipeline->id) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Lead sudah berada di pipeline ini.'
//             ]);
//         }

//         // 1️⃣ UPDATE LEAD
//         $lead->update([
//             'pipeline_id' => $toPipeline->id
//         ]);

//         // 2️⃣ INSERT PIPELINE LOG (WAJIB LENGKAP)
//         PipelineLog::create([
//             'lead_id'             => $lead->id,

//             'from_pipeline_id'    => $fromPipeline?->id,
//             'to_pipeline_id'      => $toPipeline->id,

//             'from_pipeline_name'  => $fromPipeline?->tahap,
//             'to_pipeline_name'    => $toPipeline->tahap,

//             'action'              => $action,
//             'created_by'          => $userId,
//         ]);
//     }

//     public function move(
//         Lead $lead,
//         Pipeline $to,
//         string $action = 'MANUAL'
//     ): void {

//         $from = $lead->pipeline;

//         /* =====================================================
//          | 1️⃣ GUARD — TIDAK BOLEH PINDAH KE PIPELINE YANG SAMA
//          ===================================================== */
//         if ($from && $from->id === $to->id) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Lead sudah berada di pipeline ini.'
//             ]);
//         }

//         /* =====================================================
//          | 2️⃣ UPDATE LEAD
//          ===================================================== */
//         $lead->update([
//             'pipeline_id' => $to->id,
//             'status'      => $this->resolveLeadStatus($to->tahap),
//         ]);


//         /* =====================================================
//          | 3️⃣ PIPELINE LOG (WAJIB LENGKAP)
//          ===================================================== */
//         PipelineLog::create([
//             'lead_id'            => $lead->id,

//             'from_pipeline_id'   => $from?->id,
//             'to_pipeline_id'     => $to->id,

//             'from_pipeline_name' => $from?->tahap,
//             'to_pipeline_name'   => $to->tahap,

//             'action'             => $action,
//             'created_by'         => Auth::id(),
//         ]);
//     }
//     private function resolveLeadStatus(string $pipelineTahap): string
//     {
//         return match ($pipelineTahap) {
//             'new'       => 'NEW',
//             'closing'   => 'CLOSING',
//             'lost'      => 'LOST',
//             default     => 'ACTIVE',
//         };
//     }

//     /* =====================================================
//      | MOVE BACKWARD (OPTIONAL)
//      | - hanya boleh mundur 1 tahap
//      | - cocok untuk koreksi cabang / pusat
//      ===================================================== */
//     public function moveBackward(Lead $lead, int $targetPipelineId): void
//     {
//         $this->guardNotClosed($lead);

//         $current = $this->currentPipeline($lead);
//         $target  = $this->activePipeline($targetPipelineId);

//         if ($target->urutan !== $current->urutan - 1) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Hanya boleh mundur satu tahap.'
//             ]);
//         }

//         $lead->update([
//             'pipeline_id' => $target->id,
//         ]);
//     }

//     /* =====================================================
//      | FORCE MOVE (PUSAT / SYSTEM)
//      | - bypass anti loncat
//      | - tetap validasi pipeline aktif
//      ===================================================== */
//     public function forceMove(Lead $lead, int $pipelineId): void
//     {
//         $this->guardNotClosed($lead);

//         $pipeline = $this->activePipeline($pipelineId);

//         $lead->update([
//             'pipeline_id' => $pipeline->id,
//             'status'      => 'ACTIVE',
//         ]);
//     }

//     /* =====================================================
//      | HELPER: CURRENT PIPELINE
//      ===================================================== */
//     private function currentPipeline(Lead $lead): Pipeline
//     {
//         if (!$lead->pipeline_id) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Lead belum memiliki pipeline.'
//             ]);
//         }

//         return Pipeline::where('aktif', 1)
//             ->findOrFail($lead->pipeline_id);
//     }

//     /* =====================================================
//      | HELPER: VALIDATE PIPELINE AKTIF
//      ===================================================== */
//     private function activePipeline(int $pipelineId): Pipeline
//     {
//         $pipeline = Pipeline::where('aktif', 1)->find($pipelineId);

//         if (!$pipeline) {
//             throw ValidationException::withMessages([
//                 'pipeline' => 'Pipeline tidak aktif atau tidak ditemukan.'
//             ]);
//         }

//         return $pipeline;
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
// }
