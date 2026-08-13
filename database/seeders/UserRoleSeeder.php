<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user id berdasarkan email
        $users = DB::table('users')
            ->pluck('id', 'email');

        // Ambil role id berdasarkan role_name
        $roles = DB::table('roles')
            ->pluck('id', 'role_name');

        $mapping = [
            'superadmin@domain.com' => 'SUPERADMIN',
            'admin@domain.com'      => 'ADMIN',
        ];

        foreach ($mapping as $email => $roleName) {

            if (!isset($users[$email]) || !isset($roles[$roleName])) {
                continue;
            }

            DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $users[$email],
                    'role_id' => $roles[$roleName],
                ],
                []
            );
        }
    }
}
