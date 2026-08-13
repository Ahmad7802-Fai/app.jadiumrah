<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SampleBranchSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['kode_cabang' => 'JKT'],
            [
                'nama_cabang' => 'CABANG UTAMA JAKARTA',
                'alamat'      => null,
                'kota'        => 'DKI JAKARTA',
                'is_active'   => true,
            ]
        );

        $email = env(
            'JADIUMRAH_BRANCH_ADMIN_EMAIL',
            'admin.jkt@jadiumrah.test'
        );

        $password = env('JADIUMRAH_BRANCH_ADMIN_PASSWORD');

        if (!$password) {
            throw new RuntimeException(
                'JADIUMRAH_BRANCH_ADMIN_PASSWORD wajib diisi sebelum seeding.'
            );
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'nama'      => 'CABANG UTAMA JAKARTA',
                'username'  => 'CABANGJKT',
                'password'  => Hash::make($password),
                'role'      => 'ADMIN',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );
    }
}
