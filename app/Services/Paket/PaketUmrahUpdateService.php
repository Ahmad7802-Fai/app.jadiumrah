<?php

namespace App\Services\Paket;

use App\Models\Keberangkatan;
use App\Models\PaketMaster;
use App\Models\PaketUmrah;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaketUmrahUpdateService
{
    public function update(PaketUmrah $paket, array $data): PaketUmrah
    {
        return DB::transaction(function () use ($paket, $data) {

            /**
             * 1. Resolve dependencies using the old title before
             * PaketUmrah itself is changed.
             */
            $oldTitle = $paket->title;

            $paketMaster = PaketMaster::where('nama_paket', $oldTitle)->first();

            if (! $paketMaster) {
                throw new RuntimeException(
                    "PaketMaster untuk paket '{$oldTitle}' tidak ditemukan."
                );
            }

            $activeDepartureIds = Keberangkatan::where(
                'id_paket_master',
                $paketMaster->id
            )
                ->where('status', 'Aktif')
                ->pluck('id');

            if ($activeDepartureIds->isEmpty()) {
                throw new RuntimeException(
                    "Keberangkatan aktif untuk PaketMaster ID {$paketMaster->id} tidak ditemukan."
                );
            }

            /**
             * 2. Update Paket Umrah content.
             */
            $paket->update($data);

            /**
             * 3. Sync Paket Master.
             */
            $paketMaster->update([
                'nama_paket'    => $data['title'],
                'pesawat'       => $data['pesawat'],
                'hotel_mekkah'  => $data['hotmekkah'],
                'hotel_madinah' => $data['hotmadinah'],
                'harga_quad'    => $data['quad'],
                'harga_triple'  => $data['triple'],
                'harga_double'  => $data['double'],
            ]);

            /**
             * 4. Sync every active departure belonging to the master.
             */
            Keberangkatan::whereIn('id', $activeDepartureIds)
                ->update([
                    'tanggal_berangkat' => $data['tglberangkat'],
                    'tanggal_pulang'    => now()
                        ->parse($data['tglberangkat'])
                        ->addDays($data['durasi']),
                    'kuota'             => $data['seat'],
                ]);

            return $paket;
        });
    }
}
