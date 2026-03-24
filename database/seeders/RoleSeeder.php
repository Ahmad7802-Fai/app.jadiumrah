<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission supaya tidak konflik
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [

            /*
            |--------------------------------------------------------------------------
            | SYSTEM LEVEL
            |--------------------------------------------------------------------------
            */
            'SUPERADMIN',

            /*
            |--------------------------------------------------------------------------
            | HEAD OFFICE (PUSAT)
            |--------------------------------------------------------------------------
            */
            'ADMIN_PUSAT',
            'FINANCE',

            /*
            |--------------------------------------------------------------------------
            | BRANCH LEVEL (CABANG)
            |--------------------------------------------------------------------------
            */
            'ADMIN_CABANG',
            'KEUANGAN_CABANG',
            'OPERATOR_CABANG',
            'CRM_CABANG',

            /*
            |--------------------------------------------------------------------------
            | SALES & CUSTOMER
            |--------------------------------------------------------------------------
            */
            'AGENT',
            'JAMAAH',
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role],
                ['guard_name' => 'web']
            );
        }
    }
}