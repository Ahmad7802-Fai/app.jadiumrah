<?php

namespace App\Services\Referral;

use App\Models\Agent;
use Illuminate\Http\Request;

class ReferralContextService
{
    /**
     * Tangkap referral dari URL (?agent=AGT12)
     * Dipanggil di middleware / controller halaman paket
     */
    public function capture(Request $request): void
    {
        // Jika sudah ada context, jangan override
        if (session()->has('referral_locked')) {
            return;
        }

        $agentCode = $request->query('agent');

        if (!$agentCode) {
            return;
        }

        $agent = Agent::where('kode_agent', $agentCode)
            ->where('is_active', 1)
            ->first();

        if (!$agent) {
            return;
        }

        session([
            'referral.agent_id'  => $agent->id,
            'referral.branch_id' => $agent->branch_id,
            'referral.source'    => 'agent',
            'referral.mode'      => 'affiliate',
        ]);
    }

    /**
     * Terapkan referral ke data jamaah sebelum insert
     * Dipanggil saat submit form
     */
    public function applyToJamaah(array $data): array
    {
        if (session()->has('referral.agent_id')) {
            $data['agent_id']  = session('referral.agent_id');
            $data['branch_id'] = session('referral.branch_id');
            $data['source']    = session('referral.source');
            $data['mode']      = session('referral.mode');
        } else {
            // Default: website / manual
            $data['agent_id'] = null;
            $data['source']   = 'website';
            $data['mode']     = 'manual';
        }

        // Lock supaya tidak bisa berubah setelah submit
        session(['referral_locked' => true]);

        return $data;
    }

    /**
     * Hapus referral context (opsional)
     * Dipakai setelah proses selesai
     */
    public function clear(): void
    {
        session()->forget([
            'referral.agent_id',
            'referral.branch_id',
            'referral.source',
            'referral.mode',
            'referral_locked',
        ]);
    }
}
