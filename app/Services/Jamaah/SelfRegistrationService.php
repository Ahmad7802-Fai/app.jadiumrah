<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Models\PaketUmrah;
use App\Services\JamaahService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class SelfRegistrationService
{
    public function __construct(
        protected JamaahService $jamaahService
    ) {}

    /**
     * Self registration dari website (LEAD AFFILIATE)
     */
    public function register(array $data): Jamaah
    {
        $referral = session('referral');

        if (!$referral) {
            abort(403, 'Akses pendaftaran harus melalui link resmi agen.');
        }

        // 🔐 CEK DUPLIKAT NO HP (WEBSITE ONLY)
        if (!empty($data['no_hp'])) {
            $exists = Jamaah::withoutGlobalScopes()
                ->where('no_hp', $data['no_hp'])
                ->where('source', 'website')
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'no_hp' => 'Nomor WhatsApp sudah terdaftar. Tim kami akan menghubungi Anda.',
                ]);
            }
        }

        // 🔑 AMBIL PAKET DARI REFERRAL
        $paketId = $referral['paket_id'];

        $paket = \App\Models\PaketUmrah::query()
            ->where('id', $paketId)
            ->where('status', 'Aktif')
            ->firstOrFail();

        // 🔢 NO ID
        $noId = $this->jamaahService->generateNoId();

        return Jamaah::withoutGlobalScopes()->create([
            // IDENTITAS
            'no_id'        => $noId,
            'nama_lengkap' => $data['nama_lengkap'],
            'no_hp'        => $data['no_hp'] ?? null,

            // DEFAULT WAJIB
            'nama_ayah'         => '-',
            'nik'               => '-',
            'tempat_lahir'      => '-',
            'tanggal_lahir'     => now()->subYears(30),
            'status_pernikahan' => 'Belum Menikah',
            'jenis_kelamin'     => 'L',

            // 🎯 PAKET (AUTO DARI LINK)
            'paket_id'   => $paket->id,
            'paket'      => $paket->slug,
            'nama_paket' => $paket->title,
            'tipe_kamar' => 'quad',

            // 🔗 RELASI REFERRAL
            'agent_id'  => $referral['agent_id'],
            'branch_id' => $referral['branch_id'],

            // STATUS
            'status' => 'pending',
            'mode'   => 'affiliate',
            'source' => 'website',
        ]);
    }

}

// namespace App\Services\Jamaah;

// use App\Models\Jamaah;
// use App\Services\JamaahService;
// use Illuminate\Validation\ValidationException;

// class SelfRegistrationService
// {
//     public function __construct(
//         protected JamaahService $jamaahService
//     ) {}

//     /**
//      * Self registration dari website (LEAD AFFILIATE)
//      */
//     public function register(array $data): Jamaah
//     {
//         $referral = session('referral');

//         if (!$referral) {
//             abort(403, 'Akses pendaftaran harus melalui link resmi agen.');
//         }

//         /**
//          * 🔐 BLOK NO HP DUPLIKAT (SELF REGISTRATION)
//          * - hanya source = website
//          * - tidak ganggu agent / admin
//          */
//         if (!empty($data['no_hp'])) {
//             $exists = Jamaah::withoutGlobalScopes()
//                 ->where('no_hp', $data['no_hp'])
//                 ->where('source', 'website')
//                 ->exists();

//             if ($exists) {
//                 throw ValidationException::withMessages([
//                     'no_hp' => 'Nomor WhatsApp sudah terdaftar. Tim kami akan menghubungi Anda.',
//                 ]);
//             }
//         }

//         // 🔑 SINGLE SOURCE OF TRUTH
//         $noId = $this->jamaahService->generateNoId();

//         return Jamaah::withoutGlobalScopes()->create([
//             // IDENTITAS
//             'no_id'        => $noId,
//             'nama_lengkap' => $data['nama_lengkap'],
//             'no_hp'        => $data['no_hp'] ?? null,

//             // DEFAULT WAJIB DB (AMAN)
//             'nama_ayah'         => '-',
//             'nik'               => '-',
//             'tempat_lahir'      => '-',
//             'tanggal_lahir'     => now()->subYears(30),
//             'status_pernikahan' => 'Belum Menikah',
//             'jenis_kelamin'     => 'L',

//             // PAKET BELUM DITENTUKAN
//             'paket'      => 'TBD',
//             'nama_paket' => 'TBD',
//             'tipe_kamar' => 'quad',

//             // RELASI REFERRAL
//             'agent_id'  => $referral['agent_id'],
//             'branch_id' => $referral['branch_id'],

//             // STATUS
//             'status' => 'pending',
//             'mode'   => 'affiliate',
//             'source' => 'website',
//         ]);
//     }
// }
