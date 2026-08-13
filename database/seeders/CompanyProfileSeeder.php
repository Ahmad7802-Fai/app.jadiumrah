<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\CompanyProfile;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        /* ===============================
         | ENSURE DIRECTORIES EXIST
         =============================== */
        Storage::disk('public')->makeDirectory('company/logo');
        Storage::disk('public')->makeDirectory('company/invoice');
        Storage::disk('public')->makeDirectory('company/bw');

        /* ===============================
         | DEFAULT COMPANY PROFILE
         =============================== */
        CompanyProfile::firstOrCreate(
            ['is_active' => true],
            [
                'name'        => 'PT Contoh Sejahtera Indonesia',
                'brand_name'  => 'Contoh Travel',

                'email'       => 'info@contoh.co.id',
                'phone'       => '0812-3456-7890',
                'website'     => 'https://contoh.co.id',

                'address'     => 'Jl. Contoh No. 123',
                'city'        => 'Jakarta',
                'province'    => 'DKI Jakarta',
                'postal_code' => '12345',

                'invoice_footer' => 'Terima kasih telah mempercayakan perjalanan Anda kepada kami.',
                'letter_footer'  => 'Dokumen ini diterbitkan secara sah oleh perusahaan.',

                'signature_name'     => 'Ahmad Faizi',
                'signature_position' => 'Direktur',

                'is_active' => true,
            ]
        );
    }
}
