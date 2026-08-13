<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate([
            'code' => 'HQ'
        ], [
            'name' => 'Head Office',
            'city' => 'Jakarta',
            'is_active' => true
        ]);
    }
}