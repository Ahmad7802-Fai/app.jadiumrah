<?php

namespace App\Services\Users;

use App\Models\User;
use App\Models\Agent;
use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Contracts\UserServiceInterface;

class UserService implements UserServiceInterface
{

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $role = $this->extractPrimaryRole($data);
            $branchId = $this->resolveBranchId($role, $data);

            /*
            |--------------------------------------------------------------------------
            | CREATE USER
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'branch_id' => $branchId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ASSIGN ROLE
            |--------------------------------------------------------------------------
            */

            $user->syncRoles($data['roles']);

            /*
            |--------------------------------------------------------------------------
            | DIRECT PERMISSIONS
            |--------------------------------------------------------------------------
            */

            if (!empty($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            /*
            |--------------------------------------------------------------------------
            | AUTO CREATE PROFILE
            |--------------------------------------------------------------------------
            */

            $this->createRoleProfile($user, $role, $data);

            return $user->load([
                'agentProfile',
                'websiteJamaahs'
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            $role = $this->extractPrimaryRole($data);
            $branchId = $this->resolveBranchId($role, $data);

            $user->update([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'branch_id' => $branchId,
            ]);

            if (!empty($data['password'])) {

                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            $user->syncRoles($data['roles']);

            $user->syncPermissions($data['permissions'] ?? []);

            /*
            |--------------------------------------------------------------------------
            | UPDATE PROFILE
            |--------------------------------------------------------------------------
            */

            $this->updateRoleProfile($user, $role, $data);

            return $user;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {

            $user->agentProfile()?->delete();

            Jamaah::where('user_id',$user->id)->delete();

            $user->delete();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ROLE PROFILE
    |--------------------------------------------------------------------------
    */

    private function createRoleProfile(User $user, string $role, array $data): void
    {

        /*
        |--------------------------------------------------------------------------
        | AGENT PROFILE
        |--------------------------------------------------------------------------
        */

        if ($role === 'AGENT') {

            Agent::create([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'nama' => $data['name'],
                'kode_agent' => $this->generateAgentCode(),
                'slug' => \Str::slug($data['name'].'-'.$user->id),
                'phone' => $data['phone'] ?? null,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | JAMAAH PROFILE
        |--------------------------------------------------------------------------
        */

        if ($role === 'JAMAAH') {

            Jamaah::create([
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'nama_lengkap' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'source' => 'branch'
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ROLE PROFILE
    |--------------------------------------------------------------------------
    */

    private function updateRoleProfile(User $user, string $role, array $data): void
    {

        if ($role === 'AGENT') {

            $user->agentProfile()?->update([
                'nama' => $data['name'],
                'phone' => $data['phone'] ?? null
            ]);
        }

        if ($role === 'JAMAAH') {

            Jamaah::where('user_id',$user->id)->update([
                'nama_lengkap' => $data['name'],
                'phone' => $data['phone'] ?? null
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    private function extractPrimaryRole(array $data): string
    {
        if (empty($data['roles']) || !is_array($data['roles'])) {

            throw ValidationException::withMessages([
                'roles' => 'Role wajib dipilih.'
            ]);
        }

        return $data['roles'][0];
    }


    private function resolveBranchId(string $role, array $data): ?int
    {
        $hqRoles = [
            'SUPERADMIN',
            'ADMIN_PUSAT'
        ];

        if (in_array($role,$hqRoles)) {
            return null;
        }

        return $data['branch_id'] ?? null;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE AGENT CODE
    |--------------------------------------------------------------------------
    */

    private function generateAgentCode(): string
    {
        return 'AGT-'.str_pad(
            Agent::count()+1,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}