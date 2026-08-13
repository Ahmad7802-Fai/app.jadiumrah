<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Umrah Core',
                'is_active' => true,
            ]
        );
    }
}