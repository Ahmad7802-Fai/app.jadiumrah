<?php

namespace App\Services\Branches;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BranchService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE BRANCH
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Branch
    {
        $this->authorizeSuperadmin();

        return DB::transaction(function () use ($data) {

            $branch = Branch::create([
                'name'      => $data['name'],
                'code'      => Str::upper($data['code']),
                'city'      => $data['city'] ?? null,
                'address'   => $data['address'] ?? null,
                'phone'     => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            $this->createDefaultAdminForBranch($branch);

            return $branch;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE BRANCH
    |--------------------------------------------------------------------------
    */

    public function update(Branch $branch, array $data): Branch
    {
        $this->authorizeSuperadmin();

        $branch->update($data);

        return $branch;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE BRANCH
    |--------------------------------------------------------------------------
    */

    public function delete(Branch $branch): void
    {
        $this->authorizeSuperadmin();

        if ($branch->code === 'HQ') {
            abort(403, 'Head Office cannot be deleted.');
        }

        if ($branch->users()->exists()) {
            abort(403, 'Branch still has users assigned.');
        }

        $branch->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE DEFAULT CABANG ADMIN
    |--------------------------------------------------------------------------
    */

    private function createDefaultAdminForBranch(Branch $branch): void
    {
        $email = 'admin.' . Str::slug($branch->code) . '@umrahcore.test';
        $password = Str::random(8);

        $user = User::create([
            'name'      => 'Admin ' . $branch->name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'branch_id' => $branch->id,
        ]);

        $user->assignRole('ADMIN_CABANG'); // 🔥 GANTI dari ADMIN lama

        logger("Branch Admin Created: {$email} | Password: {$password}");
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    private function authorizeSuperadmin(): void
    {
        if (!Auth::user()?->hasRole('SUPERADMIN')) {
            abort(403);
        }
    }
}