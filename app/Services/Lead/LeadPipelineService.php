<?php

namespace App\Services\Lead;

use App\Models\Lead;
use App\Models\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeadPipelineService
{
    /**
     * =====================================================
     * MANUAL / INTERNAL MOVE
     * (dipakai untuk tombol, approval, dll)
     * =====================================================
     */
    public function move(
        Lead $lead,
        Pipeline $pipeline,
        string $action,
        int $actorId
    ): void {
        DB::transaction(function () use ($lead, $pipeline, $action, $actorId) {

            // 🔒 LOCK LEAD
            $lead = Lead::lockForUpdate()->find($lead->id);

            // ❌ Tidak boleh geser jika closing / closed
            if (in_array($lead->status, ['CLOSING', 'CLOSED'])) {
                throw ValidationException::withMessages([
                    'pipeline' => 'Lead sedang atau sudah closing.'
                ]);
            }

            $oldPipeline = $lead->pipeline;

            // ❌ Anti mundur / lompat (STRICT)
            if ($oldPipeline && $pipeline->urutan <= $oldPipeline->urutan) {
                throw ValidationException::withMessages([
                    'pipeline' => 'Tidak boleh memundurkan atau melompati pipeline.'
                ]);
            }

            // ✅ Update pipeline
            $lead->update([
                'pipeline_id' => $pipeline->id,
            ]);

            // 📝 Log
            $lead->pipelineLogs()->create([
                'lead_id'            => $lead->id,
                'from_pipeline_id'   => $oldPipeline?->id,
                'from_pipeline_name' => $oldPipeline?->tahap,
                'to_pipeline_id'     => $pipeline->id,
                'to_pipeline_name'   => $pipeline->tahap,
                'action'             => $action,
                'created_by'         => $actorId,
                'changed_at'         => now(),
            ]);
        });
    }

    /**
     * =====================================================
     * AUTO MOVE DARI ACTIVITY (NON-STRICT)
     * =====================================================
     */
    public function moveByActivity(
        Lead $lead,
        string $aktivitas,
        int $actorId
    ): void {
        $map = config('lead_pipeline.activity_to_pipeline');

        // ❌ Aktivitas tidak memicu pipeline
        if (!isset($map[$aktivitas])) {
            return;
        }

        $pipeline = Pipeline::where('tahap', $map[$aktivitas])
            ->where('aktif', 1)
            ->first();

        if (!$pipeline) {
            return;
        }

        // 🔒 LOCK LEAD
        $lead = Lead::lockForUpdate()->find($lead->id);

        // ❌ Jangan geser jika closing / closed
        if (in_array($lead->status, ['CLOSING', 'CLOSED'])) {
            return;
        }

        $oldPipeline = $lead->pipeline;

        // ❌ Anti mundur / lompat (SILENT)
        if ($oldPipeline && $pipeline->urutan <= $oldPipeline->urutan) {
            return; // ⛔ DIAM, TIDAK ERROR
        }

        // ✅ Update pipeline
        $lead->update([
            'pipeline_id' => $pipeline->id,
        ]);

        // 📝 Log
        $lead->pipelineLogs()->create([
            'lead_id'            => $lead->id,
            'from_pipeline_id'   => $oldPipeline?->id,
            'from_pipeline_name' => $oldPipeline?->tahap,
            'to_pipeline_id'     => $pipeline->id,
            'to_pipeline_name'   => $pipeline->tahap,
            'action'             => 'AUTO_FROM_ACTIVITY:' . strtoupper($aktivitas),
            'created_by'         => $actorId,
            'changed_at'         => now(),
        ]);
    }
}

// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;

// class LeadPipelineService
// {
//     public function move(
//         Lead $lead,
//         Pipeline $pipeline,
//         string $action,
//         int $actorId
//     ): void {
//         DB::transaction(function () use ($lead, $pipeline, $action, $actorId) {

//             // 🔒 LOCK LEAD
//             $lead = Lead::lockForUpdate()->find($lead->id);

//             // ❌ Tidak boleh geser jika closing / closed
//             if (in_array($lead->status, ['CLOSING', 'CLOSED'])) {
//                 throw ValidationException::withMessages([
//                     'pipeline' => 'Lead sedang atau sudah closing.'
//                 ]);
//             }

//             $oldPipeline = $lead->pipeline;

//             // ❌ Anti mundur / lompat
//             if ($oldPipeline && $pipeline->urutan <= $oldPipeline->urutan) {
//                 throw ValidationException::withMessages([
//                     'pipeline' => 'Tidak boleh memundurkan atau melompati pipeline.'
//                 ]);
//             }

//             // ✅ Update pipeline SAJA
//             $lead->update([
//                 'pipeline_id' => $pipeline->id,
//             ]);

//             // 📝 Log
//             $lead->pipelineLogs()->create([
//                 'lead_id'            => $lead->id,
//                 'from_pipeline_id'   => $oldPipeline?->id,
//                 'from_pipeline_name' => $oldPipeline?->tahap,
//                 'to_pipeline_id'     => $pipeline->id,
//                 'to_pipeline_name'   => $pipeline->tahap,
//                 'action'             => $action,
//                 'created_by'         => $actorId,
//                 'changed_at'         => now(),
//             ]);
//         });
//     }

//     // 🔥 wrapper otomatis dari activity
//     public function moveByActivity(
//         Lead $lead,
//         string $aktivitas,
//         int $actorId
//     ): void {
//         $map = config('lead_pipeline.activity_to_pipeline');

//         if (!isset($map[$aktivitas])) {
//             return;
//         }

//         $pipeline = Pipeline::where('tahap', $map[$aktivitas])
//             ->where('aktif', 1)
//             ->first();

//         if (!$pipeline) {
//             return;
//         }

//         $this->move(
//             $lead,
//             $pipeline,
//             'AUTO_FROM_ACTIVITY:' . strtoupper($aktivitas),
//             $actorId
//         );
//     }
// }


// namespace App\Services\Lead;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use Illuminate\Support\Facades\DB;

// class LeadPipelineService
// {

//     public function move(
//         Lead $lead,
//         Pipeline $pipeline,
//         string $action
//     ): void {
//         DB::transaction(function () use ($lead, $pipeline, $action) {

//             // 🔒 LOCK STATUS
//             if (in_array($lead->status, ['CLOSING', 'CLOSED'])) {
//                 return;
//             }

//             $oldPipeline = $lead->pipeline;

//             // 🔒 ANTI MUNDUR / LOMPAT
//             if ($oldPipeline && $pipeline->urutan <= $oldPipeline->urutan) {
//                 return;
//             }

//             $lead->update([
//                 'pipeline_id' => $pipeline->id,
//                 'status'      => 'ACTIVE',
//             ]);

//             $lead->pipelineLogs()->create([
//                 'lead_id'            => $lead->id,
//                 'from_pipeline_id'   => $oldPipeline?->id,
//                 'from_pipeline_name' => $oldPipeline?->tahap,
//                 'to_pipeline_id'     => $pipeline->id,
//                 'to_pipeline_name'   => $pipeline->tahap,
//                 'action'             => $action,
//                 'created_by'         => auth()->id(),
//                 'changed_at'         => now(),
//             ]);
//         });
//     }

//     // 🔥 wrapper untuk activity (dipakai pusat & cabang)
//     public function moveByActivity(Lead $lead, string $aktivitas): void
//     {
//         $map = config('lead_pipeline.activity_to_pipeline');

//         if (!isset($map[$aktivitas])) {
//             return;
//         }

//         $pipeline = Pipeline::where('tahap', $map[$aktivitas])
//             ->where('aktif', 1)
//             ->first();

//         if (!$pipeline) {
//             return;
//         }

//         $this->move(
//             $lead,
//             $pipeline,
//             'AUTO_FROM_ACTIVITY:' . strtoupper($aktivitas)
//         );
//     }
// }
