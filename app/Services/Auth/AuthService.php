<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Jamaah;
use App\Models\EmailVerification;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthService
{
    protected CodeGeneratorService $code;

    public function __construct(CodeGeneratorService $code)
    {
        $this->code = $code;
    }

    // 🔥 TAMBAH INI
    public function register(array $data): User
    {
        return $this->registerJamaah($data);
    }

    // ===============================
    // 📝 REGISTER EMAIL
    // ===============================
    public function registerJamaah(array $data): User
    {
        return DB::transaction(function () use ($data) {

            // ================= USER
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // ================= ROLE
            $user->assignRole('JAMAAH');

            // ================= CODE
            $jamaahCode = $this->code->generate(
                'JMH',
                'jamaah'
            );

            // ================= JAMAAH
            Jamaah::create([
                'jamaah_code'   => $jamaahCode,
                'user_id'       => $user->id,
                'nama_lengkap'  => $data['name'],
                'phone'         => $data['phone'] ?? null,
                'source'        => 'website',
            ]);

            // ================= 🔥 CUSTOM EMAIL VERIFICATION
            $token = Str::random(64);

            EmailVerification::create([
                'email'      => $user->email,
                'token'      => $token,
                'expired_at' => now()->addMinutes(30),
            ]);

            // ================= 🔥 KIRIM EMAIL
            \Mail::raw(
                "Assalamu'alaikum {$user->name},\n\n" .
                "Klik link berikut untuk verifikasi akun:\n\n" .
                env('FRONTEND_URL') .
                "/verify?email={$user->email}&token={$token}\n\n" .
                "Link berlaku 30 menit.",
                function ($msg) use ($user) {
                    $msg->to($user->email)
                        ->subject('Verifikasi Email JadiUmrah');
                }
            );

            return $user;
        });
    }


    // ===============================
    // ✅ VERIFY EMAIL
    // ===============================
    public function verifyEmail(string $email, string $token): ?User
    {
        $verify = EmailVerification::where('email', $email)
            ->where('token', $token)
            ->latest()
            ->first();

        if (!$verify) return null;

        if (now()->gt($verify->expired_at)) return null;

        $user = User::where('email', $email)->first();

        if (!$user) return null;

        // 🔥 FIX UTAMA
        $user->email_verified_at = now();
        $user->save();
        $user->refresh();

        $verify->delete();

        return $user;
    }

    // ===============================
    // 🔐 GOOGLE LOGIN
    // ===============================
    public function googleLogin($googleUser): User
    {
        $user = User::where('google_id', $googleUser->id)->first()
            ?? User::where('email', $googleUser->email)->first();

        if (!$user) {

            // ================= CREATE USER
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(16))
            ]);

            $user->assignRole('JAMAAH');

            // ================= CODE
            $jamaahCode = $this->code->generate(
                'JMH',
                'jamaah'
            );

            // ================= JAMAAH
            Jamaah::create([
                'jamaah_code'  => $jamaahCode,
                'user_id'      => $user->id,
                'nama_lengkap' => $googleUser->name,
                'source'       => 'google'
            ]);

        } else {

            // ================= LINK GOOGLE
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'provider'  => 'google'
                ]);
            }
        }

        return $user;
    }
}