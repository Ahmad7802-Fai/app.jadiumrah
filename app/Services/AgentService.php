<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class AgentService
{
    /* ============================================================
     | CREATE AGENT
     | → AUTO USER SALES
     | → SYNC users.agent_id
     ============================================================ */
    public function create(array $data): Agent
    {
        return DB::transaction(function () use ($data) {

            $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

            foreach (['nama','email','password'] as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field {$field} wajib diisi.");
                }
            }

            $actor = auth()->user();

            /* ===============================
             | RESOLVE BRANCH
             =============================== */
            if ($actor->role === 'ADMIN') {
                if (! $actor->branch_id) {
                    throw new Exception('Admin belum terhubung ke cabang.');
                }
                $branchId = $actor->branch_id;
            } else {
                if (empty($data['branch_id'])) {
                    throw new Exception('Cabang wajib dipilih.');
                }
                $branchId = (int) $data['branch_id'];
            }

            /* ===============================
             | CREATE USER SALES (DIRECT)
             =============================== */
            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email sudah digunakan.');
            }

            $user = User::create([
                'nama'      => $data['nama'],
                'email'     => $data['email'],
                'username'  => $data['username'] ?? null,
                'password'  => Hash::make($data['password']),
                'role'      => 'SALES',
                'branch_id' => $branchId,
                'is_active' => 1,
                'agent_id'  => null, // ⛔ sementara
            ]);

            /* ===============================
             | GENERATE KODE & SLUG
             =============================== */
            $kodeAgent = $this->generateKodeAgent(
                $user->branch->kode_cabang
            );

            $slug = $this->generateUniqueSlug($data['nama']);

            $komisi = (float) ($data['komisi_persen'] ?? 0);

            /* ===============================
             | CREATE AGENT
             =============================== */
            $agent = Agent::create([
                'user_id'          => $user->id,
                'branch_id'        => $branchId,
                'nama'             => $data['nama'],
                'kode_agent'       => $kodeAgent,
                'slug'             => $slug,
                'phone'            => $data['phone'] ?? null,
                'komisi_persen'    => $komisi,
                'komisi_manual'    => $komisi,
                'komisi_affiliate' => $komisi,
                'is_active'        => 1,
            ]);

            /* ===============================
             | 🔥 SYNC BACK TO USER
             =============================== */
            $user->update([
                'agent_id' => $agent->id,
            ]);

            return $agent;
        });
    }

    /* ============================================================
     | UPDATE AGENT
     ============================================================ */
    public function update(int $agentId, array $data): Agent
    {
        return DB::transaction(function () use ($agentId, $data) {

            $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

            $agent = Agent::with('user')
                ->lockForUpdate()
                ->findOrFail($agentId);

            if (
                auth()->user()->role === 'ADMIN'
                && $agent->branch_id !== auth()->user()->branch_id
            ) {
                throw new Exception('Tidak boleh mengubah agent cabang lain.');
            }

            /* ===============================
             | UPDATE USER (SAFE)
             =============================== */
            $agent->user->update([
                'nama'  => $data['nama']  ?? $agent->user->nama,
                'email' => $data['email'] ?? $agent->user->email,
            ]);

            if (! empty($data['password'])) {
                $agent->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            /* ===============================
             | UPDATE AGENT
             =============================== */
            $update = [
                'nama'  => $data['nama']  ?? $agent->nama,
                'phone' => $data['phone'] ?? $agent->phone,
            ];

            if (array_key_exists('komisi_persen', $data)) {
                $komisi = (float) $data['komisi_persen'];
                $update['komisi_persen']    = $komisi;
                $update['komisi_manual']    = $komisi;
                $update['komisi_affiliate'] = $komisi;
            }

            $agent->update($update);

            return $agent;
        });
    }

    /* ============================================================
     | TOGGLE AGENT (SYNC USER)
     ============================================================ */
    public function toggle(int $agentId): Agent
    {
        return DB::transaction(function () use ($agentId) {

            $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

            $agent = Agent::with('user')
                ->lockForUpdate()
                ->findOrFail($agentId);

            if (
                auth()->user()->role === 'ADMIN'
                && $agent->branch_id !== auth()->user()->branch_id
            ) {
                throw new Exception('Tidak boleh mengubah agent cabang lain.');
            }

            $active = ! $agent->is_active;

            $agent->update(['is_active' => $active]);
            $agent->user->update(['is_active' => $active]);

            return $agent;
        });
    }

    /* ============================================================
     | GUARD ROLE
     ============================================================ */
    private function guardRole(array $roles): void
    {
        if (! auth()->check()) {
            throw new Exception('Unauthorized.');
        }

        if (! in_array(strtoupper(auth()->user()->role), $roles)) {
            throw new Exception('Tidak memiliki akses.');
        }
    }

    /* ============================================================
     | KODE AGENT
     ============================================================ */
    private function generateKodeAgent(string $kodeCabang): string
    {
        $prefix = 'AGT-' . $kodeCabang;

        $last = Agent::where('kode_agent', 'like', "{$prefix}-%")
            ->orderByDesc('kode_agent')
            ->value('kode_agent');

        if (! $last) {
            return "{$prefix}-001";
        }

        $number = (int) substr($last, -3) + 1;

        return "{$prefix}-" . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /* ============================================================
     | SLUG UNIQUE
     ============================================================ */
    private function generateUniqueSlug(string $nama): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $i = 1;

        while (Agent::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}

// namespace App\Services;

// use App\Models\Agent;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;
// use Exception;

// class AgentService
// {
//     public function __construct(
//         protected UserService $userService
//     ) {}

//     /* ============================================================
//      | CREATE AGENT (AUTO USER + AUTO SLUG)
//      ============================================================ */
//     public function create(array $data): Agent
//     {
//         return DB::transaction(function () use ($data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             /* ================= BASIC VALIDATION ================= */
//             foreach (['nama','email','password'] as $field) {
//                 if (empty($data[$field])) {
//                     throw new Exception("Field {$field} wajib diisi.");
//                 }
//             }

//             $authUser = auth()->user();

//             /* ================= RESOLVE BRANCH ================= */
//             if ($authUser->role === 'ADMIN') {
//                 if (! $authUser->branch_id) {
//                     throw new Exception('Admin belum terhubung ke cabang.');
//                 }
//                 $branchId = $authUser->branch_id;
//             } else {
//                 if (empty($data['branch_id'])) {
//                     throw new Exception('Cabang wajib dipilih.');
//                 }
//                 $branchId = (int) $data['branch_id'];
//             }

//             /* ================= CREATE USER (SALES) ================= */
//             $userSales = $this->userService->create([
//                 'nama'      => $data['nama'],
//                 'email'     => $data['email'],
//                 'password'  => $data['password'],
//                 'role'      => 'SALES',
//                 'branch_id' => $branchId,
//                 'username'  => $data['username'] ?? null,
//             ]);

//             /* ================= KODE AGENT ================= */
//             $kodeAgent = $this->generateKodeAgent(
//                 $userSales->branch->kode_cabang
//             );

//             /* ================= SLUG AGENT ================= */
//             $slug = $this->generateUniqueSlug($data['nama']);

//             /* ================= KOMISI ================= */
//             $komisi = (float) ($data['komisi_persen'] ?? 0);

//             /* ================= CREATE AGENT ================= */
//             return Agent::create([
//                 'user_id'          => $userSales->id,
//                 'branch_id'        => $branchId,
//                 'nama'             => $data['nama'],
//                 'kode_agent'       => $kodeAgent,
//                 'slug'             => $slug,
//                 'phone'            => $data['phone'] ?? null,

//                 'komisi_persen'    => $komisi,
//                 'komisi_manual'    => $komisi,
//                 'komisi_affiliate' => $komisi,

//                 'is_active'        => 1,
//             ]);
//         });
//     }

//     /* ============================================================
//      | UPDATE AGENT
//      ============================================================ */
//     public function update(int $agentId, array $data): Agent
//     {
//         return DB::transaction(function () use ($agentId, $data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             $authUser = auth()->user();

//             if (
//                 $authUser->role === 'ADMIN' &&
//                 $agent->branch_id !== $authUser->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             /* ================= UPDATE USER ================= */
//             $this->userService->update($agent->user->id, [
//                 'nama'     => $data['nama']     ?? null,
//                 'email'    => $data['email']    ?? null,
//                 'password' => $data['password'] ?? null,
//             ]);

//             /* ================= UPDATE AGENT ================= */
//             $update = [
//                 'nama'  => $data['nama']  ?? $agent->nama,
//                 'phone' => $data['phone'] ?? $agent->phone,
//             ];

//             // ⚠️ slug TIDAK diubah otomatis (SEO-safe)
//             // kalau mau auto-update slug, tinggal aktifkan:
//             /*
//             if (!empty($data['nama']) && $data['nama'] !== $agent->nama) {
//                 $update['slug'] = $this->generateUniqueSlug($data['nama'], $agent->id);
//             }
//             */

//             if (array_key_exists('komisi_persen', $data)) {
//                 $komisi = (float) $data['komisi_persen'];
//                 $update['komisi_persen']    = $komisi;
//                 $update['komisi_manual']    = $komisi;
//                 $update['komisi_affiliate'] = $komisi;
//             }

//             $agent->update($update);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | TOGGLE AGENT
//      ============================================================ */
//     public function toggle(int $agentId): Agent
//     {
//         return DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             $agent->update([
//                 'is_active' => ! $agent->is_active,
//             ]);

//             $this->userService->toggle($agent->user->id);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | ROLE GUARD
//      ============================================================ */
//     private function guardRole(array $roles): void
//     {
//         $role = strtoupper(auth()->user()->role ?? '');

//         if (! in_array($role, $roles)) {
//             throw new Exception('Tidak memiliki akses.');
//         }
//     }

//     /* ============================================================
//      | KODE AGENT GENERATOR
//      ============================================================ */
//     private function generateKodeAgent(string $kodeCabang): string
//     {
//         $prefix = 'AGT-' . $kodeCabang;

//         $last = Agent::where('kode_agent', 'like', $prefix.'-%')
//             ->orderBy('kode_agent', 'desc')
//             ->value('kode_agent');

//         if (! $last) {
//             return $prefix . '-001';
//         }

//         $number = (int) substr($last, -3) + 1;

//         return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
//     }

//     /* ============================================================
//      | SLUG GENERATOR (UNIQUE)
//      ============================================================ */
//     private function generateUniqueSlug(string $nama, ?int $ignoreId = null): string
//     {
//         $base = Str::slug($nama);
//         $slug = $base;
//         $i = 1;

//         while (
//             Agent::where('slug', $slug)
//                 ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
//                 ->exists()
//         ) {
//             $slug = $base . '-' . $i++;
//         }

//         return $slug;
//     }
// }


// namespace App\Services;

// use App\Models\Agent;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class AgentService
// {
//     public function __construct(
//         protected UserService $userService
//     ) {}

//     /* ============================================================
//      | CREATE AGENT (AUTO CREATE USER SALES)
//      ============================================================ */
//     public function create(array $data): Agent
//     {
//         return DB::transaction(function () use ($data) {

//             $user = auth()->user();
//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             /* ================= BASIC VALIDATION ================= */
//             foreach (['nama','email','password'] as $field) {
//                 if (empty($data[$field])) {
//                     throw new Exception("Field {$field} wajib diisi.");
//                 }
//             }

//             /* ================= RESOLVE BRANCH ================= */
//             if ($user->role === 'ADMIN') {
//                 if (! $user->branch_id) {
//                     throw new Exception('Admin belum terhubung ke cabang.');
//                 }
//                 $branchId = $user->branch_id;
//             } else {
//                 if (empty($data['branch_id'])) {
//                     throw new Exception('Cabang wajib dipilih.');
//                 }
//                 $branchId = (int) $data['branch_id'];
//             }

//             /* ================= CREATE USER (SALES) ================= */
//             $userSales = $this->userService->create([
//                 'nama'      => $data['nama'],
//                 'email'     => $data['email'],
//                 'password'  => $data['password'],
//                 'role'      => 'SALES',
//                 'branch_id' => $branchId,
//                 'username'  => $data['username'] ?? null,
//             ]);

//             /* ================= KODE AGENT ================= */
//             $kodeAgent = $this->generateKodeAgent(
//                 $userSales->branch->kode_cabang
//             );

//             /* ================= KOMISI ================= */
//             $komisi = (float) ($data['komisi_persen'] ?? 0);

//             /* ================= CREATE AGENT ================= */
//             return Agent::create([
//                 'user_id'          => $userSales->id,
//                 'branch_id'        => $branchId,
//                 'nama'             => $data['nama'],
//                 'kode_agent'       => $kodeAgent,
//                 'phone'            => $data['phone'] ?? null,

//                 'komisi_persen'    => $komisi,
//                 'komisi_manual'    => $komisi,
//                 'komisi_affiliate' => $komisi,

//                 'is_active'        => 1,
//             ]);
//         });
//     }

//     /* ============================================================
//      | UPDATE AGENT
//      ============================================================ */
//     public function update(int $agentId, array $data): Agent
//     {
//         return DB::transaction(function () use ($agentId, $data) {

//             $user = auth()->user();
//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if (
//                 $user->role === 'ADMIN' &&
//                 $agent->branch_id !== $user->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             $this->userService->update($agent->user->id, [
//                 'nama'     => $data['nama']     ?? null,
//                 'email'    => $data['email']    ?? null,
//                 'password' => $data['password'] ?? null,
//             ]);

//             $update = [
//                 'nama'  => $data['nama']  ?? $agent->nama,
//                 'phone' => $data['phone'] ?? $agent->phone,
//             ];

//             if (array_key_exists('komisi_persen', $data)) {
//                 $komisi = (float) $data['komisi_persen'];
//                 $update['komisi_persen']    = $komisi;
//                 $update['komisi_manual']    = $komisi;
//                 $update['komisi_affiliate'] = $komisi;
//             }

//             $agent->update($update);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | ROLE GUARD
//      ============================================================ */
//     private function guardRole(array $roles): void
//     {
//         $role = strtoupper(auth()->user()->role ?? '');

//         if (! in_array($role, $roles)) {
//             throw new Exception('Tidak memiliki akses.');
//         }
//     }

//     /* ============================================================
//      | KODE AGENT GENERATOR
//      ============================================================ */
//     private function generateKodeAgent(string $kodeCabang): string
//     {
//         $prefix = 'AGT-' . $kodeCabang;

//         $last = Agent::where('kode_agent', 'like', $prefix.'-%')
//             ->orderBy('kode_agent', 'desc')
//             ->value('kode_agent');

//         if (! $last) {
//             return $prefix . '-001';
//         }

//         $number = (int) substr($last, -3) + 1;

//         return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
//     }

//         public function toggle(int $agentId): Agent
//     {
//         return DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             $agent->update([
//                 'is_active' => ! $agent->is_active,
//             ]);

//             $this->userService->toggle($agent->user->id);

//             return $agent;
//         });
//     }
// }

// namespace App\Services;

// use App\Models\Agent;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class AgentService
// {
//     public function __construct(
//         protected UserService $userService
//     ) {}

//     /* ============================================================
//      | CREATE AGENT (AUTO CREATE USER SALES)
//      ============================================================ */
//     public function create(array $data): Agent
//     {
//         return DB::transaction(function () use ($data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             // ================= VALIDATION =================
//             foreach (['nama','email','password','branch_id'] as $field) {
//                 if (empty($data[$field])) {
//                     throw new Exception("Field {$field} wajib diisi.");
//                 }
//             }

//             // ================= BRANCH GUARD =================
//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 auth()->user()->branch_id !== (int) $data['branch_id']
//             ) {
//                 throw new Exception('Tidak boleh membuat agent di cabang lain.');
//             }

//             // ================= CREATE USER (SALES) =================
//             $user = $this->userService->create([
//                 'nama'      => $data['nama'],
//                 'email'     => $data['email'],
//                 'password'  => $data['password'],
//                 'role'      => 'SALES',
//                 'branch_id' => $data['branch_id'],
//                 'username'  => $data['username'] ?? null,
//             ]);

//             // ================= KODE AGENT =================
//             $kodeAgent = $this->generateKodeAgent($user->branch->kode_cabang);

//             // ================= KOMISI (SINKRON) =================
//             $komisi = (float) ($data['komisi_persen'] ?? 0);

//             // ================= CREATE AGENT =================
//             return Agent::create([
//                 'user_id'          => $user->id,
//                 'branch_id'        => $user->branch_id,
//                 'nama'             => $data['nama'],
//                 'kode_agent'       => $kodeAgent,
//                 'phone'            => $data['phone'] ?? null,

//                 // 🔑 KOMISI SINKRON
//                 'komisi_persen'    => $komisi,
//                 'komisi_manual'    => $komisi,
//                 'komisi_affiliate' => $komisi,

//                 'is_active'        => 1,
//             ]);
//         });
//     }

//     /* ============================================================
//      | UPDATE AGENT + USER (SAFE & CONSISTENT)
//      ============================================================ */
//     public function update(int $agentId, array $data): Agent
//     {
//         return DB::transaction(function () use ($agentId, $data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             // ================= BRANCH GUARD =================
//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             // ================= UPDATE USER =================
//             $this->userService->update($agent->user->id, [
//                 'nama'     => $data['nama']     ?? null,
//                 'email'    => $data['email']    ?? null,
//                 'password' => $data['password'] ?? null,
//             ]);

//             // ================= UPDATE AGENT =================
//             $update = [
//                 'nama'  => $data['nama']  ?? $agent->nama,
//                 'phone' => $data['phone'] ?? $agent->phone,
//             ];

//             // 🔑 JIKA KOMISI DIUBAH → SINKRON SEMUA
//             if (array_key_exists('komisi_persen', $data)) {
//                 $komisi = (float) $data['komisi_persen'];

//                 $update['komisi_persen']    = $komisi;
//                 $update['komisi_manual']    = $komisi;
//                 $update['komisi_affiliate'] = $komisi;
//             }

//             $agent->update($update);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | TOGGLE AGENT (SYNC USER)
//      ============================================================ */
//     public function toggle(int $agentId): Agent
//     {
//         return DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             $agent->update([
//                 'is_active' => ! $agent->is_active,
//             ]);

//             $this->userService->toggle($agent->user->id);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | DELETE AGENT (STRICT & SAFE)
//      ============================================================ */
//     public function delete(int $agentId): void
//     {
//         DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if ($agent->jamaah()->exists()) {
//                 throw new Exception('Agent memiliki jamaah.');
//             }

//             $agent->delete();
//             $agent->user->delete();
//         });
//     }

//     /* ============================================================
//      | ROLE GUARD
//      ============================================================ */
//     private function guardRole(array $roles): void
//     {
//         $role = strtoupper(auth()->user()->role ?? '');

//         if (! in_array($role, $roles)) {
//             throw new Exception('Tidak memiliki akses.');
//         }
//     }

//     /* ============================================================
//      | KODE AGENT GENERATOR
//      ============================================================ */
//     private function generateKodeAgent(string $kodeCabang): string
//     {
//         $prefix = 'AGT-' . $kodeCabang;

//         $last = Agent::where('kode_agent', 'like', $prefix.'-%')
//             ->orderBy('kode_agent', 'desc')
//             ->value('kode_agent');

//         if (! $last) {
//             return $prefix . '-001';
//         }

//         $number = (int) substr($last, -3) + 1;

//         return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
//     }
// }

// namespace App\Services;

// use App\Models\Agent;
// use Illuminate\Support\Facades\DB;
// use Exception;

// class AgentService
// {
//     public function __construct(
//         protected UserService $userService
//     ) {}

//     /* ============================================================
//      | CREATE AGENT (AUTO CREATE USER SALES)
//      ============================================================ */
//     public function create(array $data): Agent
//     {
//         return DB::transaction(function () use ($data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             foreach (['nama','email','password','branch_id'] as $f) {
//                 if (empty($data[$f])) {
//                     throw new Exception("Field {$f} wajib diisi.");
//                 }
//             }

//             // ADMIN hanya boleh cabang sendiri
//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 auth()->user()->branch_id !== (int) $data['branch_id']
//             ) {
//                 throw new Exception('Tidak boleh membuat agent di cabang lain.');
//             }

//             // 1️⃣ CREATE USER (SALES)
//             $user = $this->userService->create([
//                 'nama'      => $data['nama'],
//                 'email'     => $data['email'],
//                 'password'  => $data['password'],
//                 'role'      => 'SALES',
//                 'branch_id' => $data['branch_id'],
//                 'username'  => $data['username'] ?? null,
//             ]);

//             // 2️⃣ AUTO GENERATE KODE AGENT
//             $kodeAgent = $this->generateKodeAgent($user->branch->kode_cabang);

//             // 3️⃣ CREATE AGENT PROFILE
//             $komisi = (float) ($data['komisi_persen'] ?? 0);

//             return Agent::create([
//                 'user_id'          => $user->id,
//                 'branch_id'        => $user->branch_id,
//                 'kode_agent'       => $kodeAgent,
//                 'phone'            => $data['phone'] ?? null,

//                 // 🔑 SINKRON KOMISI
//                 'komisi_persen'    => $komisi,
//                 'komisi_manual'    => $komisi,
//                 'komisi_affiliate' => $komisi,

//                 'is_active'        => 1,
//             ]);

//         });
//     }


//     /* ============================================================
//      | UPDATE AGENT + USER
//      ============================================================ */
//     public function update(int $agentId, array $data): Agent
//     {
//         return DB::transaction(function () use ($agentId, $data) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             // ADMIN hanya cabang sendiri
//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             // UPDATE USER (LOGIN DATA)
//             $this->userService->update($agent->user->id, [
//                 'nama'     => $data['nama']     ?? null,
//                 'email'    => $data['email']    ?? null,
//                 'password' => $data['password'] ?? null,
//             ]);

//             // UPDATE AGENT PROFILE (NON-IDENTITY)
//             $update = [
//                 'phone' => $data['phone'] ?? $agent->phone,
//             ];

//             if (array_key_exists('komisi_persen', $data)) {
//                 $komisi = (float) $data['komisi_persen'];

//                 $update['komisi_persen']    = $komisi;
//                 $update['komisi_manual']    = $komisi;
//                 $update['komisi_affiliate'] = $komisi;
//             }

//             $agent->update($update);

//             return $agent;
//         });
//     }


//     /* ============================================================
//      | TOGGLE AGENT (SYNC USER)
//      ============================================================ */
//     public function toggle(int $agentId): Agent
//     {
//         return DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR','ADMIN']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             // ADMIN hanya cabang sendiri
//             if (
//                 auth()->user()->role === 'ADMIN' &&
//                 $agent->branch_id !== auth()->user()->branch_id
//             ) {
//                 throw new Exception('Tidak boleh mengubah agent cabang lain.');
//             }

//             $agent->update([
//                 'is_active' => ! $agent->is_active,
//             ]);

//             $this->userService->toggle($agent->user->id);

//             return $agent;
//         });
//     }

//     /* ============================================================
//      | DELETE AGENT (STRICT)
//      ============================================================ */
//     public function delete(int $agentId): void
//     {
//         DB::transaction(function () use ($agentId) {

//             $this->guardRole(['SUPERADMIN','OPERATOR']);

//             $agent = Agent::with('user')
//                 ->lockForUpdate()
//                 ->findOrFail($agentId);

//             if ($agent->jamaah()->exists()) {
//                 throw new Exception('Agent memiliki jamaah.');
//             }

//             $agent->delete();
//             $agent->user->delete();
//         });
//     }

//     /* ============================================================
//      | GUARD ROLE
//      ============================================================ */
//     private function guardRole(array $roles): void
//     {
//         $role = strtoupper(auth()->user()->role ?? '');

//         if (! in_array($role, $roles)) {
//             throw new Exception('Tidak memiliki akses.');
//         }
//     }

//     private function generateKodeAgent(string $kodeCabang): string
//     {
//         $prefix = 'AGT-' . $kodeCabang;

//         $last = Agent::where('kode_agent', 'like', $prefix.'-%')
//             ->orderBy('kode_agent', 'desc')
//             ->value('kode_agent');

//         if (! $last) {
//             return $prefix . '-001';
//         }

//         $number = (int) substr($last, -3) + 1;

//         return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
//     }
// }
