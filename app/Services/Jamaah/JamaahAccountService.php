<?php

namespace App\Services\Jamaah;

use App\Models\Jamaah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JamaahAccountService
{

    /*
    |--------------------------------------------------------------------------
    | CREATE ACCOUNT FROM JAMAAH
    |--------------------------------------------------------------------------
    */

    public function createAccount(Jamaah $jamaah): array
    {

        if ($jamaah->user_id) {
            throw new \Exception('Jamaah sudah memiliki akun.');
        }

        return DB::transaction(function () use ($jamaah) {

            $password = Str::random(8);

            $email = $jamaah->email
                ?? $this->generateFallbackEmail($jamaah);

            /*
            |--------------------------------------------------------------------------
            | RESOLVE BRANCH
            |--------------------------------------------------------------------------
            */

            $branchId =
                $jamaah->branch_id
                ?? $jamaah->agent?->branch_id
                ?? 1;

            /*
            |--------------------------------------------------------------------------
            | CREATE USER
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name'      => $jamaah->nama_lengkap,
                'email'     => $email,
                'password'  => Hash::make($password),
                'branch_id' => $branchId
            ]);

            $user->assignRole('JAMAAH');

            /*
            |--------------------------------------------------------------------------
            | SYNC JAMAAH
            |--------------------------------------------------------------------------
            */

            $jamaah->update([
                'user_id'   => $user->id,
                'branch_id' => $branchId
            ]);

            return [
                'user'     => $user,
                'password' => $password
            ];
        });

    }



    /*
    |--------------------------------------------------------------------------
    | SYNC USER → JAMAAH
    |--------------------------------------------------------------------------
    */

    public function syncUserJamaah(User $user): Jamaah
    {

        $jamaah = Jamaah::where('user_id',$user->id)->first();

        if($jamaah){
            return $jamaah;
        }

        return Jamaah::create([

            'user_id' => $user->id,

            'branch_id' => $user->branch_id ?? 1,

            'source' => 'self_register',

            'nama_lengkap' => $user->name,

            'email' => $user->email,

            'phone' => null,

            'approval_status' => 'approved',

            'is_active' => true

        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
    |--------------------------------------------------------------------------
    */

    public function resetPassword(Jamaah $jamaah): string
    {

        if (!$jamaah->user) {
            throw new \Exception('Jamaah belum memiliki akun.');
        }

        $newPassword = Str::random(8);

        $jamaah->user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $newPassword;

    }



    /*
    |--------------------------------------------------------------------------
    | ACTIVATE ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function activateAccount(Jamaah $jamaah): void
    {

        if (!$jamaah->user) {
            throw new \Exception('Jamaah belum memiliki akun.');
        }

        $jamaah->user->update([
            'is_active' => true
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | DEACTIVATE ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function deactivateAccount(Jamaah $jamaah): void
    {

        if (!$jamaah->user) {
            throw new \Exception('Jamaah belum memiliki akun.');
        }

        $jamaah->user->update([
            'is_active' => false
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | SEND PASSWORD VIA WHATSAPP
    |--------------------------------------------------------------------------
    */

    public function sendPasswordViaWa(Jamaah $jamaah): void
    {

        $phone = $jamaah->phone;

        if (!$phone) {
            throw new \Exception('Nomor WA tidak tersedia.');
        }

        $message = "Assalamu'alaikum {$jamaah->nama_lengkap},

Akun login Umrah Anda telah dibuat.

Email: {$jamaah->email}

Silakan login di website resmi kami.

Semoga Allah mudahkan perjalanan ibadah Anda 🤲";

        \Log::info("Kirim WA ke {$phone}: {$message}");

    }



    /*
    |--------------------------------------------------------------------------
    | GENERATE FALLBACK EMAIL
    |--------------------------------------------------------------------------
    */

    private function generateFallbackEmail(Jamaah $jamaah): string
    {

        $slug = Str::slug($jamaah->nama_lengkap);

        return $slug . '.' . $jamaah->id . '@jamaah.local';

    }

}