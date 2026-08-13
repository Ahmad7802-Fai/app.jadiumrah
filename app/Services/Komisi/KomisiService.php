<?php

namespace App\Services\Komisi;

use App\Models\Jamaah;
use App\Models\Payments;
use App\Models\KomisiLogs;
use App\Models\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class KomisiService
{
    /**
     * Generate komisi dari payment valid
     */
    public function generateFromPayment(Jamaah $jamaah, Payments $payment): void
    {
        if (!$jamaah->agent_id) return;
        if ($payment->status !== Payments::STATUS_VALID) return;

        DB::transaction(function () use ($jamaah, $payment) {

            if (KomisiLogs::where('payment_id', $payment->id)->exists()) {
                return;
            }

            $agent = Agent::find($jamaah->agent_id);
            if (!$agent) return;

            $persen = $jamaah->mode === 'affiliate'
                ? (float) $agent->komisi_affiliate
                : (float) $agent->komisi_manual;

            if ($persen <= 0) return;

            $nominal = (int) round($payment->jumlah * ($persen / 100));
            if ($nominal <= 0) return;

            KomisiLogs::create([
                'jamaah_id'      => $jamaah->id,
                'payment_id'     => $payment->id,
                'agent_id'       => $agent->id,
                'branch_id'      => $jamaah->branch_id,
                'mode'           => $jamaah->mode,
                'komisi_persen'  => $persen,
                'komisi_nominal' => $nominal,
                'status'         => KomisiLogs::STATUS_PENDING,
            ]);
        });
    }

}
