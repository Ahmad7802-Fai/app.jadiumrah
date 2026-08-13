<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleBranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ Create Branch
        $branch = Branch::firstOrCreate(
            ['kode_cabang' => 'JKT'],
            [
                'nama_cabang' => 'CABANG UTAMA JAKARTA',
                'alamat'      => 'PANCORAN JAKARTA SELATAN',
                'kota'        => 'DKI JAKARTA',
                'is_active'   => true,
            ]
        );

        // 2️⃣ Create Superadmin User
        User::firstOrCreate(
            ['email' => 'testcabangjkt@hasantour.test'],
            [
                'nama'      => 'CABANG UTAMA JAKARTA',
                'username'  => 'CABANGJKT',
                'password'  => Hash::make('admin123'),
                'role'      => 'ADMIN',
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );
    }
}
