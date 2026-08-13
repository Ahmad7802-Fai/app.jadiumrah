<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    /* ============================================================
     | CREATE USER
     | ❌ SALES TIDAK BOLEH DI SINI
     ============================================================ */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $this->guardCreate($data);

            $actor     = auth()->user();
            $actorRole = strtoupper($actor->role);
            $target    = strtoupper($data['role']);

            // 🔒 SALES DILARANG
            if ($target === 'SALES') {
                throw new Exception(
                    'User SALES wajib dibuat melalui AgentService.'
                );
            }

            $this->authorizeCreate(
                $actorRole,
                $target,
                $data['branch_id'] ?? null
            );

            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email sudah digunakan.');
            }

            $user = User::create([
                'nama'      => $data['nama'],
                'email'     => $data['email'],
                'username'  => $data['username'] ?? null,
                'password'  => Hash::make($data['password']),
                'role'      => $target,
                'branch_id' => $this->resolveBranch(
                    $actorRole,
                    $data['branch_id'] ?? null
                ),
                'is_active' => 1,

                // 🔒 TIDAK PERNAH DISET DI SINI
                'agent_id'  => null,
            ]);

            Log::info('👤 USER CREATED', [
                'user_id' => $user->id,
                'role'    => $user->role,
                'by'      => $actor->id,
            ]);

            return $user;
        });
    }

    /* ============================================================
     | UPDATE USER
     | ❌ agent_id & SALES DILINDUNGI
     ============================================================ */
    public function update(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data) {

            $user  = User::lockForUpdate()->findOrFail($userId);
            $actor = auth()->user();

            // 🔒 USER TERHUBUNG AGENT → STOP
            if ($user->agent_id) {
                throw new Exception(
                    'User terhubung dengan Agent. Gunakan AgentService.'
                );
            }

            $this->authorizeUpdate(
                strtoupper($actor->role),
                strtoupper($user->role),
                $user
            );

            // 🔒 agent_id TIDAK BOLEH DISENTUH
            unset($data['agent_id'], $data['role']);

            $user->update([
                'nama'     => $data['nama']     ?? $user->nama,
                'email'    => $data['email']    ?? $user->email,
                'username' => $data['username'] ?? $user->username,
            ]);

            if (! empty($data['password'])) {
                $user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            return $user;
        });
    }

    /* ============================================================
     | TOGGLE ACTIVE
     | ❌ AGENT USER TIDAK BOLEH
     ============================================================ */
    public function toggle(int $userId): User
    {
        return DB::transaction(function () use ($userId) {

            $user  = User::lockForUpdate()->findOrFail($userId);
            $actor = auth()->user();

            if ($user->agent_id) {
                throw new Exception(
                    'Status agent dikelola via AgentService.'
                );
            }

            $this->authorizeToggle($actor, $user);

            $user->update([
                'is_active' => ! $user->is_active,
            ]);

            return $user;
        });
    }

    /* ============================================================
     | DELETE USER
     | ❌ AGENT USER TIDAK BOLEH
     ============================================================ */
    public function delete(int $userId): void
    {
        DB::transaction(function () use ($userId) {

            $user  = User::lockForUpdate()->findOrFail($userId);
            $actor = auth()->user();

            if ($actor->role !== 'SUPERADMIN') {
                throw new Exception(
                    'Hanya SUPERADMIN boleh menghapus user.'
                );
            }

            if ($user->id === $actor->id) {
                throw new Exception(
                    'Tidak boleh menghapus diri sendiri.'
                );
            }

            // 🔒 TERHUBUNG AGENT
            if ($user->agent_id) {
                throw new Exception(
                    'User terhubung dengan Agent. Gunakan AgentService.'
                );
            }

            $user->delete();
        });
    }

    /* ============================================================
     | ===== GUARD & AUTH =====
     ============================================================ */

    private function guardCreate(array $data): void
    {
        foreach (['nama','email','password','role'] as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} wajib diisi.");
            }
        }
    }

    private function authorizeCreate(
        string $actorRole,
        string $targetRole,
        ?int $branchId
    ): void {
        $actor = auth()->user();

        if ($actorRole === 'SUPERADMIN') {
            return;
        }

        if ($actorRole === 'OPERATOR') {
            if ($targetRole === 'ADMIN') {
                throw new Exception(
                    'Operator tidak boleh membuat ADMIN.'
                );
            }
            return;
        }

        if ($actorRole === 'ADMIN') {

            if (! in_array($targetRole, [
                'KEUANGAN',
                'INVENTORY',
            ])) {
                throw new Exception(
                    'Admin hanya boleh membuat Keuangan / Inventory.'
                );
            }

            if (! $branchId || $branchId != $actor->branch_id) {
                throw new Exception(
                    'Branch tidak valid.'
                );
            }

            return;
        }

        throw new Exception('Tidak memiliki hak membuat user.');
    }

    private function authorizeUpdate(
        string $actorRole,
        string $targetRole,
        User $user
    ): void {
        if ($actorRole === 'SUPERADMIN') return;

        if ($actorRole === 'ADMIN') {

            if ($user->branch_id !== auth()->user()->branch_id) {
                throw new Exception('User beda cabang.');
            }

            if (! in_array($targetRole, [
                'KEUANGAN',
                'INVENTORY',
            ])) {
                throw new Exception(
                    'Admin hanya boleh edit Keuangan / Inventory.'
                );
            }

            return;
        }

        throw new Exception('Tidak memiliki hak edit user.');
    }

    private function authorizeToggle($actor, User $user): void
    {
        if ($actor->role === 'SUPERADMIN') return;

        if (
            $actor->role === 'ADMIN'
            && $user->branch_id === $actor->branch_id
        ) {
            return;
        }

        throw new Exception(
            'Tidak memiliki hak mengubah status user.'
        );
    }

    private function resolveBranch(
        string $actorRole,
        ?int $branchId
    ): ?int {
        if ($actorRole === 'ADMIN') {
            return auth()->user()->branch_id;
        }

        return $branchId;
    }
}

// namespace App\Services;

// use App\Models\User;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Log;
// use Exception;

// class UserService
// {
//     /* ============================================================
//      | CREATE USER (ADMIN / OPERATOR / KEUANGAN / INVENTORY)
//      ============================================================ */
//     public function create(array $data): User
//     {
//         return DB::transaction(function () use ($data) {

//             $this->guardCreate($data);

//             $ctx      = app('access.context');
//             $actor    = auth()->user();
//             $role     = strtoupper($actor->role ?? '');
//             $target   = strtoupper($data['role']);

//             /* ===============================
//              | ROLE MATRIX
//              =============================== */
//             $this->authorizeCreate($role, $target, $data['branch_id'] ?? null);

//             if (User::where('email', $data['email'])->exists()) {
//                 throw new Exception('Email sudah digunakan.');
//             }

//             $user = User::create([
//                 'nama'      => $data['nama'],
//                 'email'     => $data['email'],
//                 'username'  => $data['username'] ?? null,
//                 'password'  => Hash::make($data['password']),
//                 'role'      => $target,
//                 'branch_id' => $this->resolveBranch($role, $data['branch_id'] ?? null),
//                 'is_active' => 1,
//             ]);

//             Log::info('👤 USER CREATED', [
//                 'user_id'   => $user->id,
//                 'role'      => $user->role,
//                 'branch_id' => $user->branch_id,
//                 'by'        => $actor->id,
//             ]);

//             return $user;
//         });
//     }

//     /* ============================================================
//      | UPDATE USER
//      ============================================================ */
//     public function update(int $userId, array $data): User
//     {
//         return DB::transaction(function () use ($userId, $data) {

//             $user  = User::lockForUpdate()->findOrFail($userId);
//             $actor = auth()->user();

//             $roleActor  = strtoupper($actor->role);
//             $roleTarget = strtoupper($user->role);

//             $this->authorizeUpdate($roleActor, $roleTarget, $user);

//             $user->update([
//                 'nama'     => $data['nama']     ?? $user->nama,
//                 'email'    => $data['email']    ?? $user->email,
//                 'username' => $data['username'] ?? $user->username,
//             ]);

//             if (! empty($data['password'])) {
//                 $user->update([
//                     'password' => Hash::make($data['password']),
//                 ]);
//             }

//             return $user;
//         });
//     }

//     /* ============================================================
//      | TOGGLE ACTIVE
//      ============================================================ */
//     public function toggle(int $userId): User
//     {
//         return DB::transaction(function () use ($userId) {

//             $user  = User::lockForUpdate()->findOrFail($userId);
//             $actor = auth()->user();

//             $this->authorizeToggle($actor, $user);

//             $user->update([
//                 'is_active' => ! $user->is_active,
//             ]);

//             return $user;
//         });
//     }

//     /* ============================================================
//      | DELETE USER (STRICT)
//      ============================================================ */
//     public function delete(int $userId): void
//     {
//         DB::transaction(function () use ($userId) {

//             $user  = User::lockForUpdate()->findOrFail($userId);
//             $actor = auth()->user();

//             if (! in_array($actor->role, ['SUPERADMIN'])) {
//                 throw new Exception('Hanya SUPERADMIN boleh menghapus user.');
//             }

//             if ($user->id === $actor->id) {
//                 throw new Exception('Tidak boleh menghapus diri sendiri.');
//             }

//             if ($user->role === 'SALES') {
//                 throw new Exception('User SALES dikelola via AgentService.');
//             }

//             $user->delete();
//         });
//     }

//     /* ============================================================
//      | ===== RULE & GUARD =====
//      ============================================================ */

//     private function guardCreate(array $data): void
//     {
//         foreach (['nama','email','password','role'] as $field) {
//             if (empty($data[$field])) {
//                 throw new Exception("Field {$field} wajib diisi.");
//             }
//         }
//     }

//     private function authorizeCreate(string $actorRole, string $targetRole, ?int $branchId): void
//     {
//         $actor = auth()->user();
    
//         // SUPERADMIN bebas
//         if ($actorRole === 'SUPERADMIN') {
//             return;
//         }
    
//         // OPERATOR pusat
//         if ($actorRole === 'OPERATOR') {
//             if ($targetRole === 'ADMIN') {
//                 throw new Exception('Operator tidak boleh membuat ADMIN cabang.');
//             }
//             return;
//         }
    
//         // ADMIN cabang
//         if ($actorRole === 'ADMIN') {
    
//             if (! in_array($targetRole, ['SALES', 'KEUANGAN', 'INVENTORY'])) {
//                 throw new Exception(
//                     'Admin hanya boleh membuat SALES / Keuangan / Inventory.'
//                 );
//             }
    
//             if (! $branchId) {
//                 throw new Exception('Branch wajib diisi.');
//             }
    
//             if ((int) $branchId !== (int) $actor->branch_id) {
//                 throw new Exception('Tidak boleh membuat user di cabang lain.');
//             }
    
//             return;
//         }
    
//         throw new Exception('Tidak memiliki hak membuat user.');
//     }

//     private function authorizeUpdate(string $actorRole, string $targetRole, User $user): void
//     {
//         // SUPERADMIN bebas
//         if ($actorRole === 'SUPERADMIN') return;

//         // 🟢 SALES (AGENT) → dikelola AgentService
//         if ($targetRole === 'SALES') {
//             return;
//         }

//         if ($actorRole === 'OPERATOR') {
//             if ($targetRole === 'ADMIN') {
//                 throw new Exception('Operator tidak boleh mengubah ADMIN.');
//             }
//             return;
//         }

//         if ($actorRole === 'ADMIN') {
//             if ($user->branch_id !== auth()->user()->branch_id) {
//                 throw new Exception('User beda cabang.');
//             }

//             if (! in_array($targetRole, ['KEUANGAN','INVENTORY'])) {
//                 throw new Exception('Admin hanya boleh edit Keuangan / Inventory.');
//             }
//             return;
//         }

//         throw new Exception('Tidak memiliki hak edit user.');
//     }

//     private function authorizeToggle($actor, User $user): void
//     {
//         if ($actor->role === 'SUPERADMIN') return;

//         if ($actor->role === 'ADMIN' && $user->branch_id === $actor->branch_id) {
//             return;
//         }

//         throw new Exception('Tidak memiliki hak mengubah status user.');
//     }

//     private function resolveBranch(string $actorRole, ?int $branchId): ?int
//     {
//         if ($actorRole === 'ADMIN') {
//             return auth()->user()->branch_id;
//         }

//         return $branchId;
//     }
// }



    // private function authorizeCreate(string $actorRole, string $targetRole, ?int $branchId): void
    // {
    //     $actor = auth()->user();
    
    //     // SUPERADMIN bebas
    //     if ($actorRole === 'SUPERADMIN') {
    //         return;
    //     }
    
    //     // OPERATOR pusat
    //     if ($actorRole === 'OPERATOR') {
    //         if ($targetRole === 'ADMIN') {
    //             throw new Exception('Operator tidak boleh membuat ADMIN cabang.');
    //         }
    //         return;
    //     }
    
    //     // ADMIN cabang
    //     if ($actorRole === 'ADMIN') {
    
    //         if (! in_array($targetRole, ['SALES', 'KEUANGAN', 'INVENTORY'])) {
    //             throw new Exception(
    //                 'Admin hanya boleh membuat SALES / Keuangan / Inventory.'
    //             );
    //         }
    
    //         if (! $branchId) {
    //             throw new Exception('Branch wajib diisi.');
    //         }
    
    //         if ((int) $branchId !== (int) $actor->branch_id) {
    //             throw new Exception('Tidak boleh membuat user di cabang lain.');
    //         }
    
    //         return;
    //     }
    
    //     throw new Exception('Tidak memiliki hak membuat user.');
    // }

