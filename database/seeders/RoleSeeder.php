<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            [
                'role_name'   => 'SUPERADMIN',
                'description' => 'Akses penuh ke seluruh sistem',
            ],
            [
                'role_name'   => 'ADMIN',
                'description' => 'Akses ke dashboard dan manajemen data penting',
            ],
            [
                'role_name'   => 'OPERATOR',
                'description' => 'Dapat input data jamaah',
            ],
            [
                'role_name'   => 'KEUANGAN',
                'description' => 'Mengelola transaksi dan laporan keuangan',
            ],
            [
                'role_name'   => 'INVENTORY',
                'description' => 'Mengelola stok perlengkapan umrah',
            ],
            [
                'role_name'   => 'SALES',
                'description' => 'Mengelola CRM, follow up leads, dan closing penjualan',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                [
                    'description' => $role['description'],
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }
    }
}
