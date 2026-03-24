<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Seed Roles & Permissions
        |--------------------------------------------------------------------------
        */
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            BranchSeeder::class,
            CompanySeeder::class,
            MenuSeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Head Office Branch
        |--------------------------------------------------------------------------
        */
        $hq = Branch::firstOrCreate(
            ['code' => 'HQ'],
            [
                'name' => 'Head Office',
                'city' => 'Jakarta',
                'address' => 'Main Office',
                'phone' => '021000000',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ SUPERADMIN (Global)
        |--------------------------------------------------------------------------
        */
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@umrahcore.test'],
            [
                'name'      => 'SUPERADMIN',
                'password'  => Hash::make('password'),
                'branch_id' => null, // global
            ]
        );

        $superadmin->syncRoles(['SUPERADMIN']);

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ ADMIN PUSAT (HQ Staff - No Branch Required)
        |--------------------------------------------------------------------------
        */
        $adminPusat = User::firstOrCreate(
            ['email' => 'adminpusat@umrahcore.test'],
            [
                'name'      => 'ADMIN PUSAT',
                'password'  => Hash::make('password'),
                'branch_id' => null, // pusat tidak perlu branch
            ]
        );

        $adminPusat->syncRoles(['ADMIN_PUSAT']);

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ ADMIN CABANG (Branch Based)
        |--------------------------------------------------------------------------
        */
        $adminCabang = User::firstOrCreate(
            ['email' => 'admincabang@umrahcore.test'],
            [
                'name'      => 'ADMIN CABANG HQ',
                'password'  => Hash::make('password'),
                'branch_id' => $hq->id,
            ]
        );

        $adminCabang->syncRoles(['ADMIN_CABANG']);
    }
}