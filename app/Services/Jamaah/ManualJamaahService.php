<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Models\PaketUmrah;
use App\Models\Agent;
use Illuminate\Support\Facades\DB;
use Exception;

class ManualJamaahService
{
    /**
     * Input jamaah manual oleh agent
     */
    public function create(array $input, int $agentUserId): Jamaah
    {
        return DB::transaction(function () use ($input, $agentUserId) {

            // 🔒 Ambil agent dari user login
            $agent = Agent::where('user_id', $agentUserId)
                ->where('is_active', 1)
                ->firstOrFail();

            // 📦 Validasi paket
            $paket = PaketUmrah::where('id', $input['id_paket'])
                ->where('is_active', 1)
                ->firstOrFail();

            // 🧾 Data jamaah manual
            $jamaahData = [
                'id_paket'     => $paket->id,
                'nama_paket'   => $paket->title,
                'nama_lengkap' => $input['nama_lengkap'],
                'no_hp'        => $input['no_hp'],
                'tipe_jamaah'  => $input['tipe_jamaah'] ?? 'reguler',

                // 🔑 KUNCI MODE MANUAL
                'agent_id'     => $agent->id,
                'branch_id'    => $agent->branch_id,
                'source'       => 'agent',
                'mode'         => 'manual',

                // 🟡 PIPELINE
                'status'       => 'draft',
            ];

            // 🔢 Generate No ID Jamaah
            $jamaahData['no_id'] = $this->generateNoId();

            // 💾 Simpan jamaah
            return Jamaah::create($jamaahData);
        });
    }

    /**
     * Generate No ID Jamaah (AMAN & KONSISTEN)
     */
    protected function generateNoId(): string
    {
        $prefix = 'JMH-' . now()->format('Y');

        $last = Jamaah::where('no_id', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $number = $last
            ? ((int) substr($last->no_id, -4)) + 1
            : 1;

        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
