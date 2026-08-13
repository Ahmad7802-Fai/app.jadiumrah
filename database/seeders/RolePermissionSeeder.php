<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil role id berdasarkan role_name
        $roles = DB::table('roles')
            ->pluck('id', 'role_name');

        // Ambil permission id berdasarkan perm_key
        $perms = DB::table('permissions')
            ->pluck('id', 'perm_key');

        $mapping = [

            // ===============================
            // SUPERADMIN → SEMUA PERMISSION
            // ===============================
            'SUPERADMIN' => array_keys($perms->toArray()),

            // ===============================
            // ADMIN
            // ===============================
            'ADMIN' => [
                'paket.view',
                'paket.create',
                'paket.edit',
                'paket.delete',

                'team.view',
                'team.create',
                'team.edit',
                'team.delete',

                'partner.view',
                'partner.create',
                'partner.edit',
                'partner.delete',

                'gallery.view',
                'gallery.create',
                'gallery.edit',
                'gallery.delete',

                'testimoni.view',
                'testimoni.create',
                'testimoni.edit',
                'testimoni.delete',

                'berita.view',
                'berita.create',
                'berita.edit',
                'berita.delete',
            ],
        ];

        foreach ($mapping as $roleName => $permKeys) {

            if (!isset($roles[$roleName])) {
                continue;
            }

            foreach ($permKeys as $permKey) {

                if (!isset($perms[$permKey])) {
                    continue;
                }

                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $roles[$roleName],
                        'perm_id' => $perms[$permKey],
                    ],
                    []
                );
            }
        }
    }
}
