<?php

namespace App\Services\CompanyProfile;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CompanyProfileService
{
    /* ===============================
     | GET ACTIVE COMPANY
     =============================== */
    public function getActive(): ?CompanyProfile
    {
        return CompanyProfile::active()->first();
    }

    /* ===============================
     | CREATE / UPDATE
     =============================== */
    public function save(array $data, ?CompanyProfile $company = null): CompanyProfile
    {
        $company ??= CompanyProfile::active()->first();

        if ($company) {
            $company->update($data);
        } else {
            $company = CompanyProfile::create($data);
        }

        Cache::forget('company_profile_active');

        return $company;
    }

    /* ===============================
     | UPLOAD LOGO
     =============================== */
    public function uploadLogo(
        CompanyProfile $company,
        $file,
        string $type = 'logo'
    ): void {
        $pathMap = [
            'logo'    => 'company/logo',
            'invoice' => 'company/invoice',
            'bw'      => 'company/bw',
        ];

        $columnMap = [
            'logo'    => 'logo',
            'invoice' => 'logo_invoice',
            'bw'      => 'logo_bw',
        ];

        if (! isset($pathMap[$type])) {
            throw new \InvalidArgumentException('Invalid logo type');
        }

        $path = $file->store($pathMap[$type], 'public');

        // hapus file lama
        if ($company->{$columnMap[$type]}) {
            Storage::disk('public')->delete($company->{$columnMap[$type]});
        }

        $company->update([
            $columnMap[$type] => $path,
        ]);

        Cache::forget('company_profile_active');
    }

    /* ===============================
     | DELETE (OPTIONAL)
     =============================== */
    public function delete(CompanyProfile $company): void
    {
        $company->delete();
        Cache::forget('company_profile_active');
    }
}
