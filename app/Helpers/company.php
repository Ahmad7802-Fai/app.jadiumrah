<?php

use App\Models\CompanyProfile;
use App\Services\CompanyProfile\CompanyBankAccountService;
use Illuminate\Support\Facades\Cache;

if (! function_exists('company')) {

    /**
     * =====================================================
     * GET ACTIVE COMPANY PROFILE (CACHE FOREVER)
     * =====================================================
     */
    function company(): ?CompanyProfile
    {
        return Cache::rememberForever('company_profile_active', function () {
            return CompanyProfile::where('is_active', 1)->first();
        });
    }
}

if (! function_exists('companyBank')) {

    /**
     * =====================================================
     * GET DEFAULT COMPANY BANK BY PURPOSE
     *
     * @param string $purpose invoice|tabungan|refund|operational
     * =====================================================
     */
    function companyBank(string $purpose = 'invoice')
    {
        $company = company();

        if (! $company) {
            return null;
        }

        return Cache::rememberForever(
            "company_bank_default_{$purpose}",
            fn () => app(CompanyBankAccountService::class)
                ->getDefault($purpose, $company->id)
        );
    }
}

if (! function_exists('companyBanks')) {

    /**
     * =====================================================
     * GET ALL BANK ACCOUNTS BY PURPOSE
     * =====================================================
     */
    function companyBanks(string $purpose = 'invoice')
    {
        $company = company();

        if (! $company) {
            return collect();
        }

        return app(CompanyBankAccountService::class)
            ->list($purpose, $company->id);
    }
}

if (! function_exists('clearCompanyCache')) {

    /**
     * =====================================================
     * CLEAR ALL COMPANY RELATED CACHE
     * =====================================================
     */
    function clearCompanyCache(): void
    {
        Cache::forget('company_profile_active');

        foreach (['invoice','tabungan','refund','operational'] as $purpose) {
            Cache::forget("company_bank_default_{$purpose}");
        }
    }
}
