<?php

namespace App\Services\Paket;

use App\Models\PaketUmrah;
use App\Models\PaketMaster;
use App\Models\Keberangkatan;
use Illuminate\Support\Facades\DB;

class PaketUmrahDeleteService
{
    public function delete(PaketUmrah $paket): void
    {
        DB::transaction(function () use ($paket) {

            // 1️⃣ Nonaktifkan Paket Umrah
            $paket->update([
                'status'    => 'Tidak Aktif',
                'is_active' => '0',
            ]);

            // 2️⃣ Nonaktifkan Paket Master
            PaketMaster::where('nama_paket', $paket->title)
                ->update([
                    'is_active' => '0',
                ]);

            // 3️⃣ Tutup semua keberangkatan aktif
            Keberangkatan::whereIn('id_paket_master', function ($q) use ($paket) {
                    $q->select('id')
                      ->from('paket_master')
                      ->where('nama_paket', $paket->title);
                })
                ->where('status', 'Aktif')
                ->update([
                    'status' => 'Batal',
                ]);
        });
    }
}
