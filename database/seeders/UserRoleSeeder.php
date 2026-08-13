<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')
            ->pluck('id', 'role_name');

        $users = DB::table('users')
            ->select(['id', 'role'])
            ->whereNotNull('role')
            ->get();

        foreach ($users as $user) {
            if (!isset($roles[$user->role])) {
                continue;
            }

            DB::table('user_roles')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $roles[$user->role],
                ],
                []
            );
        }
    }
}
