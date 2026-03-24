<?php

namespace App\Services\Company;

use App\Models\CompanyProfile;

class CompanyQueryService
{
    public function get(): ?CompanyProfile
    {
        return CompanyProfile::with('bankAccounts')->first();
    }
}