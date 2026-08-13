<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Models\JamaahDocument;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class JamaahService
{
    public function __construct(
        protected CodeGeneratorService $codeService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE JAMAAH
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Jamaah
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();

            if (!$user) {
                throw new RuntimeException('User tidak terautentikasi.');
            }

            $data = $this->normalizeData($data);

            /*
            |--------------------------------------------------------------------------
            | ROLE OWNERSHIP
            |--------------------------------------------------------------------------
            */

            if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
                $data['source'] = $data['source'] ?? 'offline';
            } elseif ($user->hasRole('ADMIN_CABANG')) {
                $data['branch_id'] = $user->branch_id;
                $data['agent_id'] = null;
                $data['user_id'] = null;
                $data['source'] = 'branch';
            } elseif ($user->hasRole('AGENT')) {
                $data['branch_id'] = $user->branch_id;
                $data['agent_id'] = $user->id;
                $data['user_id'] = null;
                $data['source'] = 'agent';
            } else {
                $data['user_id'] = $user->id;
                $data['branch_id'] = null;
                $data['agent_id'] = null;
                $data['source'] = 'website';
            }

            /*
            |--------------------------------------------------------------------------
            | SYSTEM FIELDS
            |--------------------------------------------------------------------------
            */

            $data['jamaah_code'] = $this->codeService->generate(
                prefix: 'JMH',
                entity: 'jamaah',
                pad: 5,
                yearly: true
            );

            $data['approval_status'] = $data['approval_status'] ?? 'pending';
            $data['is_active'] = array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : true;

            return Jamaah::create($data);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO CREATE (WEBSITE / REFERRAL SYSTEM)
    |--------------------------------------------------------------------------
    */

    public function autoCreate(array $data, ?User $agent = null): Jamaah
    {
        return DB::transaction(function () use ($data, $agent) {
            $data = $this->normalizeData($data);

            $data['jamaah_code'] = $this->codeService->generate(
                prefix: 'JMH',
                entity: 'jamaah',
                pad: 5,
                yearly: true
            );

            $data['approval_status'] = 'pending';
            $data['is_active'] = true;

            if ($agent) {
                $data['source'] = 'agent';
                $data['agent_id'] = $agent->id;
                $data['branch_id'] = $agent->branch_id;
                $data['user_id'] = null;
            } else {
                $data['source'] = 'website';
                $data['agent_id'] = null;
                $data['branch_id'] = null;
            }

            return Jamaah::create($data);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE JAMAAH
    |--------------------------------------------------------------------------
    */

    public function update(Jamaah $jamaah, array $data): Jamaah
    {
        return DB::transaction(function () use ($jamaah, $data) {
            $user = auth()->user();

            if (!$user) {
                throw new RuntimeException('User tidak terautentikasi.');
            }

            $data = $this->normalizeData($data);

            /*
            |--------------------------------------------------------------------------
            | SUPERADMIN / ADMIN PUSAT
            |--------------------------------------------------------------------------
            */

            if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
                $jamaah->update($data);
                return $jamaah->refresh();
            }

            /*
            |--------------------------------------------------------------------------
            | ADMIN CABANG
            |--------------------------------------------------------------------------
            */

            if ($user->hasRole('ADMIN_CABANG')) {
                if ($jamaah->branch_id && (int) $jamaah->branch_id !== (int) $user->branch_id) {
                    throw new RuntimeException('Tidak memiliki akses ke jamaah ini.');
                }

                unset(
                    $data['branch_id'],
                    $data['agent_id'],
                    $data['user_id'],
                    $data['source']
                );

                $jamaah->update($data);

                return $jamaah->refresh();
            }

            /*
            |--------------------------------------------------------------------------
            | AGENT
            |--------------------------------------------------------------------------
            */

            if ($user->hasRole('AGENT')) {
                if ((int) $jamaah->agent_id !== (int) $user->id) {
                    throw new RuntimeException('Tidak memiliki akses ke jamaah ini.');
                }

                unset(
                    $data['branch_id'],
                    $data['agent_id'],
                    $data['user_id'],
                    $data['approval_status'],
                    $data['source']
                );

                $jamaah->update($data);

                return $jamaah->refresh();
            }

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER / JAMAAH WEBSITE
            |--------------------------------------------------------------------------
            */

            if ($user->hasRole(['CUSTOMER', 'JAMAAH'])) {
                if ((int) $jamaah->user_id !== (int) $user->id) {
                    throw new RuntimeException('Tidak memiliki akses ke jamaah ini.');
                }

                unset(
                    $data['branch_id'],
                    $data['agent_id'],
                    $data['approval_status'],
                    $data['source'],
                    $data['user_id']
                );

                $jamaah->update($data);

                return $jamaah->refresh();
            }

            throw new RuntimeException('Unauthorized.');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE JAMAAH
    |--------------------------------------------------------------------------
    */

    public function delete(Jamaah $jamaah): void
    {
        DB::transaction(function () use ($jamaah) {
            if ((string) $jamaah->approval_status === 'approved') {
                throw new RuntimeException('Jamaah yang sudah disetujui tidak dapat dihapus.');
            }

            if (method_exists($jamaah, 'bookings') && $jamaah->bookings()->exists()) {
                throw new RuntimeException('Jamaah sudah terhubung ke booking dan tidak dapat dihapus.');
            }

            $jamaah->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL SYSTEM
    |--------------------------------------------------------------------------
    */

    public function approve(Jamaah $jamaah): Jamaah
    {
        $user = auth()->user();

        if (!$user) {
            throw new RuntimeException('User tidak terautentikasi.');
        }

        if (!$user->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'ADMIN_CABANG',
        ])) {
            throw new RuntimeException('Tidak memiliki izin approve.');
        }

        $jamaah->update([
            'approval_status' => 'approved',
        ]);

        return $jamaah->refresh();
    }

    public function reject(Jamaah $jamaah): Jamaah
    {
        $user = auth()->user();

        if (!$user) {
            throw new RuntimeException('User tidak terautentikasi.');
        }

        if (!$user->hasRole([
            'SUPERADMIN',
            'ADMIN_PUSAT',
            'ADMIN_CABANG',
        ])) {
            throw new RuntimeException('Tidak memiliki izin reject.');
        }

        $jamaah->update([
            'approval_status' => 'rejected',
        ]);

        return $jamaah->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT UPLOAD
    |--------------------------------------------------------------------------
    */

    public function uploadDocument(
        Jamaah $jamaah,
        string $type,
        $file,
        ?string $expiredAt = null,
        ?string $note = null
    ): JamaahDocument {
        $path = $file->store('jamaah-documents', 'public');

        return JamaahDocument::updateOrCreate(
            [
                'jamaah_id' => $jamaah->id,
                'document_type' => $type,
            ],
            [
                'file_path' => $path,
                'expired_at' => $expiredAt,
                'note' => $note,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZATION
    |--------------------------------------------------------------------------
    */

    protected function normalizeData(array $data): array
    {
        if (array_key_exists('nama_lengkap', $data)) {
            $data['nama_lengkap'] = $this->nullableTrim($data['nama_lengkap']);
        }

        if (array_key_exists('tempat_lahir', $data)) {
            $data['tempat_lahir'] = $this->nullableTrim($data['tempat_lahir']);
        }

        if (array_key_exists('address', $data)) {
            $data['address'] = $this->nullableTrim($data['address']);
        }

        if (array_key_exists('city', $data)) {
            $data['city'] = $this->nullableTrim($data['city']);
        }

        if (array_key_exists('province', $data)) {
            $data['province'] = $this->nullableTrim($data['province']);
        }

        if (array_key_exists('family_id', $data)) {
            $data['family_id'] = $this->nullableTrim($data['family_id']);
        }

        if (array_key_exists('seat_number', $data)) {
            $data['seat_number'] = $this->nullableTrim($data['seat_number']);
        }

        if (array_key_exists('gender', $data)) {
            $data['gender'] = $this->normalizeGender($data['gender']);
        }

        if (array_key_exists('nik', $data)) {
            $data['nik'] = $this->normalizeNik($data['nik']);
        }

        if (array_key_exists('passport_number', $data)) {
            $data['passport_number'] = $this->normalizePassport($data['passport_number']);
        }

        if (array_key_exists('phone', $data)) {
            $data['phone'] = $this->normalizePhone($data['phone']);
        }

        if (array_key_exists('email', $data)) {
            $data['email'] = $this->normalizeEmail($data['email']);
        }

        return $data;
    }

    protected function normalizeGender($value): ?string
    {
        $value = $this->nullableTrim($value);

        if ($value === null) {
            return null;
        }

        $g = mb_strtolower($value);

        return match ($g) {
            'l', 'lk', 'laki', 'laki laki', 'laki-laki', 'male', 'pria' => 'laki-laki',
            'p', 'pr', 'perempuan', 'wanita', 'female' => 'perempuan',
            default => $g,
        };
    }

    protected function normalizeNik($value): ?string
    {
        $value = $this->nullableTrim($value);

        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\D+/', '', (string) $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizePassport($value): ?string
    {
        $value = $this->nullableTrim($value);

        if ($value === null) {
            return null;
        }

        return strtoupper($value);
    }

    protected function normalizePhone($value): ?string
    {
        $value = $this->nullableTrim($value);

        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/', '', (string) $value);

        return $value !== '' ? $value : null;
    }

    protected function normalizeEmail($value): ?string
    {
        $value = $this->nullableTrim($value);

        if ($value === null) {
            return null;
        }

        return mb_strtolower($value);
    }

    protected function nullableTrim($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}