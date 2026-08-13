<?php

namespace App\Services\Payout;

use App\Models\AgentPayoutRequest;
use App\Models\AgentPayoutTransfer;
use App\Models\KomisiLogs;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PayoutPaymentService
{
    public function pay(int $payoutId, int $adminId): void
    {
        DB::transaction(function () use ($payoutId, $adminId) {

            /**
             * ==================================================
             * 1️⃣ LOCK PAYOUT + AGENT
             * ==================================================
             */
            $payout = AgentPayoutRequest::with('agent')
                ->lockForUpdate()
                ->findOrFail($payoutId);

            if ($payout->status !== AgentPayoutRequest::STATUS_APPROVED) {
                throw new RuntimeException(
                    'Payout belum disetujui atau sudah diproses.'
                );
            }

            $agent = $payout->agent;

            /**
             * ==================================================
             * 2️⃣ VALIDASI DATA REKENING
             * ==================================================
             */
            if (! $agent || ! $agent->hasValidBankAccount()) {
                throw new RuntimeException(
                    'Data rekening agent belum lengkap.'
                );
            }

            /**
             * ==================================================
             * 3️⃣ LOCK & VALIDASI KOMISI
             * ==================================================
             */
            $komisiQuery = KomisiLogs::where('payout_request_id', $payout->id)
                ->where('agent_id', $payout->agent_id)
                ->where('status', KomisiLogs::STATUS_REQUESTED)
                ->lockForUpdate();

            if (! $komisiQuery->exists()) {
                throw new RuntimeException(
                    'Tidak ada komisi valid untuk dibayarkan.'
                );
            }

            $totalKomisi = (int) $komisiQuery->sum('komisi_nominal');

            if ($totalKomisi !== (int) $payout->total_komisi) {
                throw new RuntimeException(
                    'Total komisi payout tidak konsisten.'
                );
            }

            /**
             * ==================================================
             * 4️⃣ UPDATE KOMISI → PAID
             * ==================================================
             */
            $komisiQuery->update([
                'status' => KomisiLogs::STATUS_PAID,
            ]);

            /**
             * ==================================================
             * 5️⃣ SNAPSHOT TRANSFER (AUDIT PROOF)
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
             * 6️⃣ UPDATE PAYOUT → PAID
             * ==================================================
             */
            $payout->update([
                'status'  => AgentPayoutRequest::STATUS_PAID,
                'paid_at' => now(),
                'paid_by' => $adminId,
            ]);
        });
    }
}
