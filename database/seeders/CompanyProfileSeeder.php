<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('company/logo');
        Storage::disk('public')->makeDirectory('company/invoice');
        Storage::disk('public')->makeDirectory('company/bw');

        CompanyProfile::firstOrCreate(
            ['is_active' => true],
            [
                'name'        => 'JadiUmrah',
                'brand_name'  => 'JadiUmrah',

                'email'       => null,
                'phone'       => null,
                'website'     => null,

                'address'     => null,
                'city'        => null,
                'province'    => null,
                'postal_code' => null,

                'invoice_footer' => 'Terima kasih telah mempercayakan perjalanan Anda kepada JadiUmrah.',
                'letter_footer'  => 'Dokumen ini diterbitkan secara sah oleh JadiUmrah.',

                'signature_name'     => null,
                'signature_position' => null,

                'is_active' => true,
            ]
        );
    }
}
