<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Jamaah;
use App\Models\EmailVerification;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Str;

class AuthService
{
    protected CodeGeneratorService $code;

    public function __construct(CodeGeneratorService $code)
    {
        $this->code = $code;
    }

    public function register(array $data): User
    {
        return $this->registerJamaah($data);
    }

    // ===============================
    // 📝 REGISTER JAMAAH (FINAL FIX)
    // ===============================
    public function registerJamaah(array $data): User
    {
        // 🔥 STEP 1: TRANSACTION (DB ONLY)
        [$user, $token] = DB::transaction(function () use ($data) {

            // ================= USER
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // ================= ROLE
            $user->assignRole('JAMAAH');

            // ================= CODE
            $jamaahCode = $this->code->generate('JMH', 'jamaah');

            // ================= JAMAAH
            Jamaah::create([
                'jamaah_code'   => $jamaahCode,
                'user_id'       => $user->id,
                'nama_lengkap'  => $data['name'],
                'phone'         => $data['phone'] ?? null,
                'source'        => 'website',
            ]);

            // ================= TOKEN VERIFICATION
            $token = Str::random(64);

            EmailVerification::create([
                'email'      => $user->email,
                'token'      => $token,
                'expired_at' => now()->addMinutes(30),
            ]);

            return [$user, $token];
        });

        // ===============================
        // 🔗 BUILD VERIFY LINK
        // ===============================
        $frontend = config('app.frontend_url') ?: 'http://localhost:3000';

        $link = $frontend .
            "/verify?email=" . urlencode($user->email) .
            "&token={$token}";

        // ===============================
        // 📧 SEND EMAIL (IMPROVED UX 🔥)
        // ===============================
        try {
            Mail::raw(
                "Assalamu'alaikum {$user->name},\n\n" .

                "Selamat datang di JadiUmrah ✨\n\n" .

                "Silakan verifikasi akun Anda dengan klik link berikut:\n\n" .

                "{$link}\n\n" .

                "⏳ Link ini hanya berlaku selama 30 menit.\n\n" .

                "Jika tombol/link tidak bisa diklik, silakan copy & paste ke browser Anda.\n\n" .

                "Jika Anda tidak merasa mendaftar, abaikan email ini.\n\n" .

                "Barakallahu fiikum 🤲\n" .
                "Tim JadiUmrah",
                
                function ($msg) use ($user) {
                    $msg->to($user->email)
                        ->subject('Verifikasi Akun JadiUmrah');
                }
            );

        } catch (\Throwable $e) {
            // 🔥 LOG ERROR TANPA GAGALKAN REGISTER
            Log::error('MAIL ERROR: ' . $e->getMessage());
        }

        return $user;
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