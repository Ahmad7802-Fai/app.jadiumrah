<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env(
            'JADIUMRAH_SUPERADMIN_EMAIL',
            'superadmin@jadiumrah.test'
        );

        $password = env('JADIUMRAH_SUPERADMIN_PASSWORD');

        if (!$password) {
            throw new RuntimeException(
                'JADIUMRAH_SUPERADMIN_PASSWORD wajib diisi sebelum seeding.'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'nama'      => 'SUPER ADMIN',
                'password'  => Hash::make($password),
                'role'      => 'SUPERADMIN',
                'branch_id' => null,
                'is_active' => 1,
            ]
        );
    }
}
