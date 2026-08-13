<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // USER
            ['perm_key' => 'user.view', 'perm_name' => 'Melihat daftar user'],
            ['perm_key' => 'user.create', 'perm_name' => 'Membuat user'],
            ['perm_key' => 'user.edit', 'perm_name' => 'Mengedit user'],
            ['perm_key' => 'user.delete', 'perm_name' => 'Menghapus user'],
            ['perm_key' => 'user.reset_password', 'perm_name' => 'Reset password user'],

            // PAKET
            ['perm_key' => 'paket.view', 'perm_name' => 'Melihat daftar paket umrah'],
            ['perm_key' => 'paket.create', 'perm_name' => 'Menambah paket umrah'],
            ['perm_key' => 'paket.edit', 'perm_name' => 'Mengedit paket umrah'],
            ['perm_key' => 'paket.delete', 'perm_name' => 'Menghapus paket umrah'],

            // JAMAAH
            ['perm_key' => 'jamaah.view', 'perm_name' => 'Melihat data jamaah'],
            ['perm_key' => 'jamaah.create', 'perm_name' => 'Menambah jamaah'],
            ['perm_key' => 'jamaah.edit', 'perm_name' => 'Mengedit jamaah'],

            // KEUANGAN & INVENTORY
            ['perm_key' => 'transaksi.view', 'perm_name' => 'Melihat transaksi keuangan'],
            ['perm_key' => 'inventory.view', 'perm_name' => 'Melihat inventory'],

            // TEAM
            ['perm_key' => 'team.view', 'perm_name' => 'Melihat daftar team'],
            ['perm_key' => 'team.create', 'perm_name' => 'Menambah team'],
            ['perm_key' => 'team.edit', 'perm_name' => 'Mengedit team'],
            ['perm_key' => 'team.delete', 'perm_name' => 'Menghapus team'],

            // PARTNER
            ['perm_key' => 'partner.view', 'perm_name' => 'Melihat daftar partner'],
            ['perm_key' => 'partner.create', 'perm_name' => 'Menambah partner'],
            ['perm_key' => 'partner.edit', 'perm_name' => 'Mengedit partner'],
            ['perm_key' => 'partner.delete', 'perm_name' => 'Menghapus partner'],

            // GALLERY
            ['perm_key' => 'gallery.view', 'perm_name' => 'Melihat daftar gallery'],
            ['perm_key' => 'gallery.create', 'perm_name' => 'Menambah gallery'],
            ['perm_key' => 'gallery.edit', 'perm_name' => 'Mengedit gallery'],
            ['perm_key' => 'gallery.delete', 'perm_name' => 'Menghapus gallery'],

            // TESTIMONI
            ['perm_key' => 'testimoni.view', 'perm_name' => 'Melihat testimoni'],
            ['perm_key' => 'testimoni.create', 'perm_name' => 'Menambah testimoni'],
            ['perm_key' => 'testimoni.edit', 'perm_name' => 'Mengedit testimoni'],
            ['perm_key' => 'testimoni.delete', 'perm_name' => 'Menghapus testimoni'],

            // BERITA
            ['perm_key' => 'berita.view', 'perm_name' => 'Melihat daftar berita'],
            ['perm_key' => 'berita.create', 'perm_name' => 'Menambah berita'],
            ['perm_key' => 'berita.edit', 'perm_name' => 'Mengedit berita'],
            ['perm_key' => 'berita.delete', 'perm_name' => 'Menghapus berita'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['perm_key' => $permission['perm_key']],
                ['perm_name' => $permission['perm_name']]
            );
        }
    }
}
