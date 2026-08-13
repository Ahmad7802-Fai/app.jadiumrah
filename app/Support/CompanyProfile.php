<?php

namespace App\Support;

trait CompanyProfile
{
    protected function companyProfile(): array
    {
        return [
            'name'    => config('company.name'),
            'address' => config('company.address'),
            'website' => config('company.website'),

            'bank' => config('company.bank'),
        ];
    }
}
