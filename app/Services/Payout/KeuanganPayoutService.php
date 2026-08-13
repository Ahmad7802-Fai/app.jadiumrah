<?php

namespace App\Services\Payout;

use App\Models\AgentPayoutRequest;
use App\Models\KomisiLogs;
use App\Models\AgentPayoutTransfer;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KeuanganPayoutService
{
    /**
     * ==================================================
     * APPROVE PAYOUT
     * requested → approved
     * komisi requested → approved
     * ==================================================
     */
    public function approve(int $payoutId, int $adminId): AgentPayoutRequest
    {
        return DB::transaction(function () use ($payoutId, $adminId) {

            $payout = AgentPayoutRequest::lockForUpdate()
                ->findOrFail($payoutId);

            if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
                throw new RuntimeException('Payout tidak bisa di-approve.');
            }

            $komisiQuery = KomisiLogs::where('payout_request_id', $payout->id)
                ->where('agent_id', $payout->agent_id)
                ->where('status', KomisiLogs::STATUS_REQUESTED)
                ->lockForUpdate();

            if (! $komisiQuery->exists()) {
                throw new RuntimeException('Tidak ada komisi valid untuk payout ini.');
            }

            // ✅ CUKUP UPDATE PAYOUT
            $payout->update([
                'status'      => AgentPayoutRequest::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => $adminId,
            ]);

            return $payout;
        });
    }

    /**
     * ==================================================
     * PAY PAYOUT
     * approved → paid
     * komisi approved → paid
     * snapshot transfer (audit-proof)
     * ==================================================
     */
    public function pay(int $payoutId, int $adminId): AgentPayoutRequest
    {
        return DB::transaction(function () use ($payoutId, $adminId) {

            /**
             * ==================================================
             * 1️⃣ LOCK PAYOUT + AGENT
             * ==================================================
             */
            $payout = AgentPayoutRequest::lockForUpdate()
                ->with('agent')
                ->findOrFail($payoutId);

            if ($payout->status !== AgentPayoutRequest::STATUS_APPROVED) {
                throw new RuntimeException('Payout belum di-approve.');
            }

            $agent = $payout->agent;

            /**
             * ==================================================
             * 2️⃣ VALIDASI DATA REKENING AGENT
             * ==================================================
             */
            if (! $agent || ! $agent->hasValidBankAccount()) {
                throw new RuntimeException('Data rekening agent belum lengkap.');
            }

            /**
             * ==================================================
             * 3️⃣ LOCK KOMISI (HARUS STATUS: REQUESTED)
             * ==================================================
             */
            $komisiQuery = KomisiLogs::where('payout_request_id', $payout->id)
                ->where('agent_id', $payout->agent_id)
                ->where('status', KomisiLogs::STATUS_REQUESTED)
                ->lockForUpdate();

            if (! $komisiQuery->exists()) {
                throw new RuntimeException('Tidak ada komisi valid untuk dibayarkan.');
            }

            /**
             * ==================================================
             * 4️⃣ VALIDASI TOTAL & JUMLAH ITEM (ANTI MANIPULASI)
             * ==================================================
             */
            $totalKomisi = (int) $komisiQuery->sum('komisi_nominal');
            $totalItem   = (int) $komisiQuery->count();

            if ($totalKomisi !== (int) $payout->total_komisi) {
                throw new RuntimeException('Total komisi payout tidak konsisten.');
            }

            if ($totalItem !== (int) $payout->total_item) {
                throw new RuntimeException('Jumlah item komisi tidak sesuai.');
            }

            /**
             * ==================================================
             * 5️⃣ UPDATE KOMISI → PAID (LEDGER FINAL)
             * ==================================================
             */
            $komisiQuery->update([
                'status'     => KomisiLogs::STATUS_PAID,
                'updated_at' => now(),
            ]);

            /**
             * ==================================================
             * 6️⃣ SNAPSHOT TRANSFER (AUDIT-PROOF)
             * ==================================================
             */
            AgentPayoutTransfer::create([
                'payout_id'           => $payout->id,
                'bank_name'           => $agent->bank_name,
                'bank_account_number' => $agent->bank_account_number,
                'bank_account_name'   => $agent->bank_account_name,
                'amount'              => $totalKomisi,
                'paid_at'             => now(),
                'paid_by'             => $adminId,
            ]);

            /**
             * ==================================================
             * 7️⃣ UPDATE PAYOUT HEADER (TERAKHIR)
             * ==================================================
             */
            $payout->update([
                'status'  => AgentPayoutRequest::STATUS_PAID,
                'paid_at' => now(),
                'paid_by' => $adminId,
            ]);

            return $payout;
        });
    }
    
    /**
     * ==================================================
     * REJECT PAYOUT
     * requested → rejected
     * komisi requested → available
     * ==================================================
     */
    public function reject(
        int $payoutId,
        int $adminId,
        ?string $reason = null
    ): AgentPayoutRequest {
        return DB::transaction(function () use ($payoutId, $adminId, $reason) {

            // 🔒 LOCK PAYOUT
            $payout = AgentPayoutRequest::lockForUpdate()
                ->findOrFail($payoutId);

            if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
                throw new RuntimeException('Payout tidak bisa ditolak.');
            }

            // 🔄 ROLLBACK KOMISI
            KomisiLogs::where('payout_request_id', $payout->id)
                ->where('agent_id', $payout->agent_id)
                ->where('status', KomisiLogs::STATUS_REQUESTED)
                ->update([
                    'status'            => KomisiLogs::STATUS_AVAILABLE,
                    'payout_request_id' => null,
                ]);

            // ❌ UPDATE PAYOUT
            $payout->update([
                'status'        => AgentPayoutRequest::STATUS_REJECTED,
                'rejected_at'   => now(),
                'rejected_by'   => $adminId,
                'reject_reason' => $reason,
            ]);

            return $payout;
        });
    }
}


// namespace App\Services\Payout;

// use App\Models\AgentPayoutRequest;
// use App\Models\KomisiLogs;
// use Illuminate\Support\Facades\DB;
// use RuntimeException;
// use App\Models\AgentPayoutTransfer;
// use App\Models\Agent;
// class KeuanganPayoutService
// {
//     /**
//      * ==================================================
//      * APPROVE PAYOUT
//      * requested → approved
//      * ==================================================
//      */
//     public function approve(int $payoutId, int $adminId): AgentPayoutRequest
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             // 🔒 LOCK PAYOUT
//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
//                 throw new RuntimeException('Payout tidak bisa di-approve.');
//             }

//             // 🔒 VALIDASI KOMISI (HARUS ADA & VALID)
//             $komisiQuery = KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('agent_id', $payout->agent_id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->lockForUpdate();

//             if (! $komisiQuery->exists()) {
//                 throw new RuntimeException('Tidak ada komisi valid untuk payout ini.');
//             }

//             $payout->update([
//                 'status'      => AgentPayoutRequest::STATUS_APPROVED,
//                 'approved_at' => now(),
//                 'approved_by' => $adminId,
//             ]);

//             return $payout;
//         });
//     }

//     /**
//      * ==================================================
//      * PAY PAYOUT
//      * approved → paid
//      * komisi requested → paid
//      * ==================================================
//      */
//     public function pay(int $payoutId, int $adminId): AgentPayoutRequest
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->with('agent')
//                 ->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_APPROVED) {
//                 throw new RuntimeException('Payout belum di-approve.');
//             }

//             $agent = $payout->agent;

//             // 🔒 VALIDASI BANK (DOUBLE SAFETY)
//             if (
//                 ! $agent->bank_name ||
//                 ! $agent->bank_account_number ||
//                 ! $agent->bank_account_name
//             ) {
//                 throw new RuntimeException('Data rekening agent tidak lengkap.');
//             }

//             // 🔒 KOMISI
//             $komisi = KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->lockForUpdate();

//             if (! $komisi->exists()) {
//                 throw new RuntimeException('Tidak ada komisi valid.');
//             }

//             $total = (int) $komisi->sum('komisi_nominal');

//             // ✅ UPDATE KOMISI
//             $komisi->update(['status' => KomisiLogs::STATUS_PAID]);

//             // ✅ CREATE TRANSFER SNAPSHOT (INI KUNCI)
//             AgentPayoutTransfer::create([
//                 'payout_id'           => $payout->id,
//                 'bank_name'           => $agent->bank_name,
//                 'bank_account_number' => $agent->bank_account_number,
//                 'bank_account_name'   => $agent->bank_account_name,
//                 'amount'              => $total,
//                 'paid_at'             => now(),
//                 'paid_by'             => $adminId,
//             ]);

//             // ✅ UPDATE PAYOUT
//             $payout->update([
//                 'status'  => AgentPayoutRequest::STATUS_PAID,
//                 'paid_at' => now(),
//                 'paid_by' => $adminId,
//             ]);

//             return $payout;
//         });
//     }
//     /**
//      * ==================================================
//      * REJECT PAYOUT
//      * requested → rejected
//      * komisi requested → approved
//      * ==================================================
//      */
//     public function reject(
//         int $payoutId,
//         int $adminId,
//         ?string $reason = null
//     ): AgentPayoutRequest {
//         return DB::transaction(function () use ($payoutId, $adminId, $reason) {

//             // 🔒 LOCK PAYOUT
//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
//                 throw new RuntimeException('Payout tidak bisa ditolak.');
//             }

//             // 🔓 ROLLBACK KOMISI
//             KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('agent_id', $payout->agent_id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->update([
//                     'status'            => KomisiLogs::STATUS_AVAILABLE,
//                     'payout_request_id' => null,
//                 ]);

//             // ❌ UPDATE PAYOUT
//             $payout->update([
//                 'status'        => AgentPayoutRequest::STATUS_REJECTED,
//                 'rejected_at'   => now(),
//                 'rejected_by'   => $adminId,
//                 'reject_reason' => $reason,
//             ]);

//             return $payout;
//         });
//     }
// }

// namespace App\Services\Payout;

// use App\Models\AgentPayoutRequest;
// use App\Models\KomisiLogs;
// use Illuminate\Support\Facades\DB;
// use RuntimeException;

// class KeuanganPayoutService
// {
//     /**
//      * ==================================================
//      * APPROVE PAYOUT (ADMINISTRATIF)
//      * requested → approved
//      * ==================================================
//      */
//     public function approve(int $payoutId, int $adminId): AgentPayoutRequest
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             // ❌ tidak boleh approve selain requested
//             if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
//                 throw new RuntimeException('Payout tidak bisa di-approve.');
//             }

//             // ❌ safety: payout harus punya komisi
//             if (! $payout->komisiLogs()->exists()) {
//                 throw new RuntimeException('Payout tidak memiliki komisi.');
//             }

//             $payout->update([
//                 'status'      => AgentPayoutRequest::STATUS_APPROVED,
//                 'approved_at' => now(),
//                 'approved_by' => $adminId,
//             ]);

//             return $payout;
//         });
//     }

//     /**
//      * ==================================================
//      * PAY PAYOUT (UANG KELUAR)
//      * approved → paid
//      * komisi requested → paid
//      * ==================================================
//      */
//     public function pay(int $payoutId, int $adminId): AgentPayoutRequest
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             // ❌ hanya payout approved yang boleh dibayar
//             if ($payout->status !== AgentPayoutRequest::STATUS_APPROVED) {
//                 throw new RuntimeException('Payout belum di-approve.');
//             }

//             // 🔒 ambil komisi yang valid untuk dibayar
//             $komisiQuery = KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->lockForUpdate();

//             if (! $komisiQuery->exists()) {
//                 throw new RuntimeException('Tidak ada komisi valid untuk dibayar.');
//             }

//             // 💸 update payout
//             $payout->update([
//                 'status'   => AgentPayoutRequest::STATUS_PAID,
//                 'paid_at'  => now(),
//                 'paid_by'  => $adminId,
//             ]);

//             // 💰 update semua komisi → paid
//             $komisiQuery->update([
//                 'status' => KomisiLogs::STATUS_PAID,
//             ]);

//             return $payout;
//         });
//     }

//     /**
//      * ==================================================
//      * REJECT PAYOUT (OPSIONAL, TAPI DISIAPKAN)
//      * requested → rejected
//      * komisi requested → available
//      * ==================================================
//      */
//     public function reject(
//         int $payoutId,
//         int $adminId,
//         ?string $reason = null
//     ): AgentPayoutRequest {
//         return DB::transaction(function () use ($payoutId, $adminId, $reason) {

//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
//                 throw new RuntimeException('Payout tidak bisa ditolak.');
//             }

//             // 🔓 kembalikan komisi ke available
//             KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->update([
//                     'status'             => KomisiLogs::STATUS_AVAILABLE,
//                     'payout_request_id'  => null,
//                 ]);

//             $payout->update([
//                 'status'       => AgentPayoutRequest::STATUS_REJECTED,
//                 'rejected_at'  => now(),
//                 'rejected_by'  => $adminId,
//                 'reject_reason'=> $reason,
//             ]);

//             return $payout;
//         });
//     }
// }


// namespace App\Services\Payout;

// use App\Models\AgentPayoutRequest;
// use App\Models\KomisiLogs;
// use Illuminate\Support\Facades\DB;
// use Exception;
// use RuntimeException;
// class KeuanganPayoutService
// {
//     public function approve(int $payoutId, int $adminId): AgentPayoutRequest
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             $payout = AgentPayoutRequest::lockForUpdate()->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_REQUESTED) {
//                 throw new RuntimeException('Payout tidak bisa di-approve.');
//             }

//             if (!$payout->komisiLogs()->exists()) {
//                 throw new RuntimeException('Payout tidak memiliki komisi.');
//             }

//             $payout->update([
//                 'status'      => AgentPayoutRequest::STATUS_APPROVED,
//                 'approved_at' => now(),
//                 'approved_by' => $adminId,
//             ]);

//             return $payout;
//         });
//     }

//     public function pay(int $payoutId, int $adminId)
//     {
//         return DB::transaction(function () use ($payoutId, $adminId) {

//             $payout = AgentPayoutRequest::lockForUpdate()
//                 ->findOrFail($payoutId);

//             if ($payout->status !== AgentPayoutRequest::STATUS_APPROVED) {
//                 throw new RuntimeException('Payout belum di-approve.');
//             }

//             // Update payout
//             $payout->update([
//                 'status'  => AgentPayoutRequest::STATUS_PAID,
//                 'paid_at'=> now(),
//                 'paid_by'=> $adminId,
//             ]);

//             // Update semua komisi
//             KomisiLogs::where('payout_request_id', $payout->id)
//                 ->where('status', KomisiLogs::STATUS_REQUESTED)
//                 ->update([
//                     'status' => KomisiLogs::STATUS_PAID,
//                 ]);

//             return $payout;
//         });
//     }

// }
