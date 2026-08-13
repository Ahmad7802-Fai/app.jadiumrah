<?php

namespace App\Services\Paket;

use App\Models\PaketUmrah;
use App\Models\PaketMaster;
use App\Models\Keberangkatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaketUmrahService
{
    /**
     * Create Paket Umrah + Paket Master + Keberangkatan (ATOMIC)
     */
    public function create(array $data): PaketUmrah
    {
        return DB::transaction(function () use ($data) {

            /**
             * Normalize data
             */
            $slug = $data['slug']
                ?? Str::slug($data['title']) . '-' . time();

            $tanggalBerangkat = Carbon::parse($data['tglberangkat']);
            $tanggalPulang    = $tanggalBerangkat->copy()->addDays($data['durasi']);

            /**
             * 1️⃣ CREATE PAKET MASTER
             */
            $paketMaster = PaketMaster::create([
                'nama_paket'     => $data['title'],
                'pesawat'        => $data['pesawat'],
                'hotel_mekkah'   => $data['hotmekkah'],
                'hotel_madinah'  => $data['hotmadinah'],
                'harga_quad'     => $data['quad'],
                'harga_triple'   => $data['triple'],
                'harga_double'   => $data['double'],
                'diskon_default' => 0,
                'is_active'      => '1',
            ]);

            /**
             * 2️⃣ CREATE KEBERANGKATAN
             */
            Keberangkatan::create([
                'id_paket_master'    => $paketMaster->id,
                'kode_keberangkatan' => 'PKU-' . now()->format('ymd-His'),
                'tanggal_berangkat'  => $tanggalBerangkat,
                'tanggal_pulang'     => $tanggalPulang,
                'kuota'              => $data['seat'],
                'seat_terisi'        => 0,
                'jumlah_jamaah'      => 0,
                'status'             => 'Aktif',
            ]);

            /**
             * 3️⃣ CREATE PAKET UMRAH
             */
            return PaketUmrah::create([
                'title'               => $data['title'],
                'slug'                => $slug,
                'seo_title'           => $data['seo_title'] ?? $data['title'],
                'tglberangkat'        => $tanggalBerangkat,
                'pesawat'             => $data['pesawat'],
                'flight'              => $data['flight'],
                'durasi'              => $data['durasi'],
                'seat'                => $data['seat'],
                'hotmekkah'           => $data['hotmekkah'],
                'rathotmekkah'        => $data['rathotmekkah'],
                'hotmadinah'          => $data['hotmadinah'],
                'rathotmadinah'       => $data['rathotmadinah'],
                'quad'                => $data['quad'],
                'triple'              => $data['triple'],
                'double'              => $data['double'],
                'itin'                => $data['itin'],
                'photo'               => $data['photo'] ?? null,
                'thaif'               => $data['thaif'] ?? 'Tidak',
                'dubai'               => $data['dubai'] ?? 'Tidak',
                'kereta'              => $data['kereta'] ?? 'Tidak',
                'deskripsi'           => $data['deskripsi'],
                'status'              => $data['status'] ?? 'Aktif',
                'is_active'           => '1',
                'allow_self_register' => $data['allow_self_register'] ?? 1,
            ]);
        });
    }
}
