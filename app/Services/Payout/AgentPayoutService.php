<?php

namespace App\Services\Payout;

use App\Models\KomisiLogs;
use App\Models\AgentPayoutRequest;
use App\Models\Agent;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AgentPayoutService
{
    /**
     * ==================================================
     * AGENT AJUKAN PENCAIRAN KOMISI
     * ==================================================
     */
    public function request(int $agentId, int $userId): AgentPayoutRequest
    {
        return DB::transaction(function () use ($agentId, $userId) {

            /**
             * ==================================================
             * 1️⃣ LOAD AGENT + VALIDASI REKENING
             * ==================================================
             */
            $agent = Agent::findOrFail($agentId);

            if (! $agent->hasValidBankAccount()) {
                throw new RuntimeException(
                    'Lengkapi data rekening bank sebelum mengajukan pencairan komisi.'
                );
            }

            /**
             * ==================================================
             * 2️⃣ CEK PAYOUT MASIH AKTIF
             * ==================================================
             */
            $hasActivePayout = AgentPayoutRequest::where('agent_id', $agentId)
                ->whereIn('status', [
                    AgentPayoutRequest::STATUS_REQUESTED,
                    AgentPayoutRequest::STATUS_APPROVED,
                ])
                ->exists();

            if ($hasActivePayout) {
                throw new RuntimeException(
                    'Masih ada pencairan komisi yang sedang diproses.'
                );
            }

            /**
             * ==================================================
             * 3️⃣ AMBIL KOMISI STATUS AVAILABLE (LOCK ROW)
             * ==================================================
             */
            $komisi = KomisiLogs::where('agent_id', $agentId)
                ->where('branch_id', $agent->branch_id)
                ->where('status', KomisiLogs::STATUS_AVAILABLE)
                ->lockForUpdate()
                ->get();

            if ($komisi->isEmpty()) {
                throw new RuntimeException(
                    'Belum ada komisi yang dapat dicairkan.'
                );
            }

            /**
             * ==================================================
             * 4️⃣ CREATE PAYOUT REQUEST (HEADER)
             * ==================================================
             */
            $payout = AgentPayoutRequest::create([
                'agent_id'     => $agent->id,
                'branch_id'    => $agent->branch_id,
                'total_komisi' => $komisi->sum('komisi_nominal'),
                'total_item'   => $komisi->count(),
                'status'       => AgentPayoutRequest::STATUS_REQUESTED,
                'requested_at' => now(),
                'requested_by' => $userId,
            ]);

            /**
             * ==================================================
             * 5️⃣ UPDATE KOMISI → REQUESTED (LOCKED)
             * ==================================================
             */
            KomisiLogs::whereIn('id', $komisi->pluck('id'))
                ->update([
                    'status'            => KomisiLogs::STATUS_REQUESTED,
                    'payout_request_id' => $payout->id,
                    'requested_at'      => now(),
                ]);

            return $payout;
        });
    }
}

// namespace App\Services\Payout;

// use App\Models\KomisiLogs;
// use App\Models\AgentPayoutRequest;
// use App\Models\Agent;
// use Illuminate\Support\Facades\DB;
// use RuntimeException;

// class AgentPayoutService
// {
//     /**
//      * Agent submit payout request
//      */
//     public function request(int $agentId, int $userId): AgentPayoutRequest
// {
//     return DB::transaction(function () use ($agentId, $userId) {

//         /**
//          * =====================================================
//          * 1️⃣ LOAD AGENT + VALIDASI REKENING
//          * =====================================================
//          */
//         $agent = Agent::findOrFail($agentId);

//         if (! $agent->hasValidBankAccount()) {
//             throw new RuntimeException(
//                 'Lengkapi data rekening sebelum mengajukan pencairan komisi.'
//             );
//         }

//         /**
//          * =====================================================
//          * 2️⃣ GUARD: MASIH ADA PAYOUT AKTIF
//          * =====================================================
//          */
//         $exists = AgentPayoutRequest::where('agent_id', $agentId)
//             ->whereIn('status', [
//                 AgentPayoutRequest::STATUS_REQUESTED,
//                 AgentPayoutRequest::STATUS_APPROVED,
//             ])
//             ->exists();

//         if ($exists) {
//             throw new RuntimeException(
//                 'Masih ada pencairan yang sedang diproses. Silakan tunggu.'
//             );
//         }

//         /**
//          * =====================================================
//          * 3️⃣ AMBIL KOMISI AVAILABLE (LOCK)
//          * =====================================================
//          */
//         $komisi = KomisiLogs::where('agent_id', $agentId)
//             ->where('branch_id', $agent->branch_id)
//             ->where('status', KomisiLogs::STATUS_AVAILABLE)
//             ->lockForUpdate()
//             ->get();

//         if ($komisi->isEmpty()) {
//             throw new RuntimeException(
//                 'Belum ada komisi yang bisa dicairkan.'
//             );
//         }

//         /**
//          * =====================================================
//          * 4️⃣ CREATE PAYOUT REQUEST (HEADER)
//          * =====================================================
//          */
//         $payout = AgentPayoutRequest::create([
//             'agent_id'     => $agent->id,
//             'branch_id'    => $agent->branch_id,
//             'total_komisi' => $komisi->sum('komisi_nominal'),
//             'total_item'   => $komisi->count(),
//             'status'       => AgentPayoutRequest::STATUS_REQUESTED,
//             'requested_at' => now(),
//             'requested_by' => $userId,
//         ]);

//         /**
//          * =====================================================
//          * 5️⃣ LOCK KOMISI → REQUESTED
//          * =====================================================
//          */
//         KomisiLogs::whereIn('id', $komisi->pluck('id'))
//             ->update([
//                 'status'            => KomisiLogs::STATUS_REQUESTED,
//                 'payout_request_id' => $payout->id,
//                 'requested_at'      => now(),
//             ]);

//         return $payout;
//     });
// }

// }
