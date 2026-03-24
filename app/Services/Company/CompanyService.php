<?php

namespace App\Services\Company;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    public function update(array $data): CompanyProfile
    {
        return DB::transaction(function () use ($data) {

            $company = CompanyProfile::first();

            if (!$company) {
                $company = CompanyProfile::create($data);
            } else {
                $company->update($data);
            }

            return $company;
        });
    }
}