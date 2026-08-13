<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CORE MASTER DATA
        |--------------------------------------------------------------------------
        */

        // Roles harus lebih dulu
        $this->call(RoleSeeder::class);

        // Permissions
        $this->call(PermissionSeeder::class);

        // Pivot role_permissions
        $this->call(RolePermissionSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | SYSTEM BASE DATA
        |--------------------------------------------------------------------------
        */

        // Branch contoh / default
        $this->call(SampleBranchSeeder::class);

        // User Super Admin (biasanya butuh role & branch)
        $this->call(SuperAdminSeeder::class);

        // Pivot user_roles
        $this->call(UserRoleSeeder::class);

        $this->call(CompanyProfileSeeder::class);
    }
}
