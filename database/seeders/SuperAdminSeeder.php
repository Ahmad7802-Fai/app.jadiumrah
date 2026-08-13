<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@superadmin.com'],
            [
                'nama'      => 'SUPER ADMIN',
                'password'  => Hash::make('superadmin123'),
                'role'      => 'SUPERADMIN',
                'branch_id' => null,
                'is_active' => 1,
            ]
        );
    }
}
