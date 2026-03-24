<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            /*
            |--------------------------------------------------------------------------
            | UPDATE USERS
            |--------------------------------------------------------------------------
            */

            $user->update([
                'name'  => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);

            /*
            |--------------------------------------------------------------------------
            | AUTO DETECT PROFILE RELATION
            |--------------------------------------------------------------------------
            */

            if ($user->agentProfile) {
                $this->updateAgentProfile($user, $data);
            }

            if ($user->jamaahProfile) {
                $this->updateJamaahProfile($user, $data);
            }

            return $user->fresh()->load([
                'branch',
                'roles',
                'agentProfile',
                'jamaahProfile',
            ]);
        });
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Password lama tidak sesuai.'],
            ]);
        }

        if (($data['current_password'] ?? null) === ($data['new_password'] ?? null)) {
            throw ValidationException::withMessages([
                'new_password' => ['Password baru harus berbeda dari password lama.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);
    }

    protected function updateAgentProfile(User $user, array $data): void
    {
        $agent = $user->agentProfile;

        if (! $agent) {
            return;
        }

        $agent->update([
            'nama'                => $data['nama'] ?? $agent->nama,
            'phone'               => $data['phone'] ?? $agent->phone,
            'bank_name'           => $data['bank_name'] ?? $agent->bank_name,
            'bank_account_number' => $data['bank_account_number'] ?? $agent->bank_account_number,
            'bank_account_name'   => $data['bank_account_name'] ?? $agent->bank_account_name,
        ]);
    }

    protected function updateJamaahProfile(User $user, array $data): void
    {
        $jamaah = $user->jamaahProfile;

        if (! $jamaah) {
            return;
        }

        $jamaah->update([
            'nama_lengkap'    => $data['nama_lengkap'] ?? $jamaah->nama_lengkap,
            'gender'          => $data['gender'] ?? $jamaah->gender,
            'tanggal_lahir'   => $data['tanggal_lahir'] ?? $jamaah->tanggal_lahir,
            'tempat_lahir'    => $data['tempat_lahir'] ?? $jamaah->tempat_lahir,
            'nik'             => $data['nik'] ?? $jamaah->nik,
            'passport_number' => $data['passport_number'] ?? $jamaah->passport_number,
            'phone'           => $data['phone'] ?? $jamaah->phone,
            'email'           => $data['email'] ?? $jamaah->email,
            'address'         => $data['address'] ?? $jamaah->address,
            'city'            => $data['city'] ?? $jamaah->city,
            'province'        => $data['province'] ?? $jamaah->province,
        ]);
    }
}