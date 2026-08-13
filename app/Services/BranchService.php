<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BranchService
{
    public function __construct(
        protected UserService $userService
    ) {}

    /* ============================================================
     | CREATE CABANG + AUTO ADMIN USER
     ============================================================ */
    public function create(array $data): Branch
    {
        return DB::transaction(function () use ($data) {

            /* ===============================
            | GUARDS
            =============================== */
            $this->guardRole(['SUPERADMIN', 'OPERATOR']);
            $this->guardCreate($data);

            /* ===============================
            | GENERATE KODE CABANG
            =============================== */
            $kodeCabang = $this->generateKodeCabang(
                strtoupper($data['prefix'])
            );

            /* ===============================
            | CREATE BRANCH
            =============================== */
            $branch = Branch::create([
                'kode_cabang' => $kodeCabang,
                'nama_cabang' => $data['nama_cabang'],
                'alamat'      => $data['alamat'] ?? null,
                'kota'        => $data['kota'] ?? null,
                'is_active'   => 1,
            ]);

            /* ===============================
            | LINK CREATOR KE BRANCH
            | (ANTI branch_id NULL)
            =============================== */
            $user = auth()->user();

            if ($user && in_array($user->role, ['OPERATOR', 'ADMIN'])) {
                if (! $user->branch_id) {
                    $user->update([
                        'branch_id' => $branch->id
                    ]);
                }
            }

            /* ===============================
            | AUTO CREATE ADMIN CABANG
            =============================== */
            $this->userService->create([
                'nama'      => 'Admin ' . $branch->nama_cabang,
                'email'     => $data['admin_email'],
                'password'  => $data['admin_password'],
                'role'      => 'ADMIN',
                'branch_id' => $branch->id,
            ]);

            /* ===============================
            | LOG
            =============================== */
            Log::info('CABANG_CREATED', [
                'branch_id' => $branch->id,
                'kode'      => $kodeCabang,
                'by_user'   => auth()->id(),
            ]);

            return $branch;
        });
    }

    // public function create(array $data): Branch
    // {
    //     return DB::transaction(function () use ($data) {

    //         $this->guardRole(['SUPERADMIN', 'OPERATOR']);
    //         $this->guardCreate($data);

    //         // 🔥 AUTO GENERATE KODE CABANG
    //         $kodeCabang = $this->generateKodeCabang(strtoupper($data['prefix']));

    //         $branch = Branch::create([
    //             'kode_cabang' => $kodeCabang,
    //             'nama_cabang' => $data['nama_cabang'],
    //             'alamat'      => $data['alamat'] ?? null,
    //             'kota'        => $data['kota'] ?? null,
    //             'is_active'   => 1,
    //         ]);

    //         // 🔐 AUTO CREATE ADMIN CABANG
    //         $this->userService->create([
    //             'nama'      => 'Admin ' . $branch->nama_cabang,
    //             'email'     => $data['admin_email'],
    //             'password'  => $data['admin_password'],
    //             'role'      => 'ADMIN',
    //             'branch_id' => $branch->id,
    //         ]);

    //         Log::info('CABANG_CREATED', [
    //             'branch_id' => $branch->id,
    //             'kode'      => $kodeCabang,
    //             'by_user'   => auth()->id(),
    //         ]);

    //         return $branch;
    //     });
    // }


    /* ============================================================
     | UPDATE CABANG
     ============================================================ */
    public function update(int $branchId, array $data): Branch
    {
        return DB::transaction(function () use ($branchId, $data) {

            $this->guardRole(['SUPERADMIN', 'OPERATOR']);
            $this->guardUpdate($data);

            $branch = Branch::lockForUpdate()->findOrFail($branchId);

            $branch->update([
                'nama_cabang' => $data['nama_cabang'],
                'alamat'      => $data['alamat'] ?? null,
                'kota'        => $data['kota'] ?? null,
            ]);

            Log::info('CABANG_UPDATED', [
                'branch_id' => $branch->id,
                'by_user'   => auth()->id(),
            ]);

            return $branch;
        });
    }


    /* ============================================================
     | TOGGLE CABANG (SYNC VIA USER SERVICE)
     ============================================================ */
    public function toggle(int $branchId): Branch
    {
        return DB::transaction(function () use ($branchId) {

            $this->guardRole(['SUPERADMIN', 'OPERATOR']);

            $branch = Branch::with('users')
                ->lockForUpdate()
                ->findOrFail($branchId);

            $newStatus = ! $branch->is_active;

            /* ===============================
             | TOGGLE BRANCH
             =============================== */
            $branch->update([
                'is_active' => $newStatus,
            ]);

            /* ===============================
             | SYNC USERS VIA UserService
             =============================== */
            foreach ($branch->users as $user) {
                if ($user->is_active !== $newStatus) {
                    $this->userService->toggle($user->id);
                }
            }

            Log::warning('CABANG_TOGGLED', [
                'branch_id' => $branch->id,
                'status'    => $newStatus ? 'ACTIVE' : 'INACTIVE',
                'by_user'   => auth()->id(),
            ]);

            return $branch;
        });
    }

    /* ============================================================
     | DELETE CABANG (STRICT)
     ============================================================ */
    public function delete(int $branchId): void
    {
        DB::transaction(function () use ($branchId) {

            $this->guardRole(['SUPERADMIN']);

            $branch = Branch::with(['users', 'jamaah'])
                ->lockForUpdate()
                ->findOrFail($branchId);

            if ($branch->users()->exists()) {
                throw new Exception('Cabang tidak bisa dihapus karena masih memiliki user.');
            }

            if ($branch->jamaah()->exists()) {
                throw new Exception('Cabang tidak bisa dihapus karena masih memiliki jamaah.');
            }

            $branch->delete();

            Log::alert('CABANG_DELETED', [
                'branch_id' => $branchId,
                'by_user'   => auth()->id(),
            ]);
        });
    }

    /* ============================================================
     | =================== GUARDS ===================
     ============================================================ */

    private function guardCreate(array $data): void
    {
        foreach (['prefix','nama_cabang','admin_email','admin_password'] as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} wajib diisi.");
            }
        }
    }


    private function guardUpdate(array $data): void
    {
        if (empty($data['nama_cabang'])) {
            throw new Exception('Nama cabang wajib diisi.');
        }
    }


    private function guardRole(array $roles): void
    {
        $ctx  = app('access.context');
        $role = strtoupper($ctx['role'] ?? '');

        if (! in_array($role, $roles)) {
            throw new Exception('Tidak memiliki akses.');
        }
    }

    private function generateKodeCabang(string $prefix): string
    {
        $last = Branch::where('kode_cabang', 'like', $prefix.'-%')
            ->orderBy('kode_cabang', 'desc')
            ->value('kode_cabang');

        if (! $last) {
            return $prefix . '-01';
        }

        $number = (int) substr($last, -2) + 1;

        return $prefix . '-' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }

}
