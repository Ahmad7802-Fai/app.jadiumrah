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
use App\Mail\VerifyEmailMail;
use Illuminate\Validation\ValidationException;

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
    // ✅ REGISTER JAMAAH
    // ===============================
    public function registerJamaah(array $data): User
    {
        $existingUser = User::where('email', $data['email'])->first();

        // ===============================
        // 🔥 CASE 1: SUDAH ADA & BELUM VERIFY → RESEND
        // ===============================
        if ($existingUser && !$existingUser->email_verified_at) {

            // 🔥 RATE LIMIT (ANTI SPAM)
            $last = EmailVerification::where('email', $existingUser->email)
                ->latest()
                ->first();

            if ($last && now()->diffInSeconds($last->created_at) < 60) {
                throw ValidationException::withMessages([
                    'email' => ['Tunggu 1 menit sebelum kirim ulang']
                ]);
            }

            Log::info("Resend verification: {$existingUser->email}");

            // 🔥 hapus token lama
            EmailVerification::where('email', $existingUser->email)->delete();

            // 🔥 buat token baru
            $token = Str::random(64);

            EmailVerification::create([
                'email'      => $existingUser->email,
                'token'      => $token,
                'expired_at' => now()->addMinutes(30),
            ]);

            $this->sendVerificationEmail($existingUser, $token);

            return $existingUser;
        }

        // ===============================
        // 🔥 CASE 2: SUDAH ADA & SUDAH VERIFY → ERROR
        // ===============================
        if ($existingUser && $existingUser->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah terdaftar']
            ]);
        }

        // ===============================
        // 🔥 CASE 3: USER BARU
        // ===============================
        [$user, $token] = DB::transaction(function () use ($data) {

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],

                // 🔥 FIX: HANDLE PASSWORD NULL
                'password' => Hash::make(
                    $data['password'] ?? Str::random(8)
                ),
            ]);

            $user->assignRole('JAMAAH');

            $jamaahCode = $this->code->generate('JMH', 'jamaah');

            Jamaah::create([
                'jamaah_code'   => $jamaahCode,
                'user_id'       => $user->id,
                'nama_lengkap'  => $data['name'],
                'phone'         => $data['phone'] ?? null,
                'source'        => 'website',
            ]);

            $token = Str::random(64);

            EmailVerification::create([
                'email'      => $user->email,
                'token'      => $token,
                'expired_at' => now()->addMinutes(30),
            ]);

            return [$user, $token];
        });

        $this->sendVerificationEmail($user, $token);

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

        // 🔥 FIX: ANTI DOUBLE VERIFY
        if ($user->email_verified_at) {
            return $user;
        }

        $user->email_verified_at = now();
        $user->save();
        $user->refresh();

        $verify->delete();

        Log::info("Email verified: {$email}");

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

            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'provider' => 'google',
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(16))
            ]);

            $user->assignRole('JAMAAH');

            $jamaahCode = $this->code->generate('JMH', 'jamaah');

            Jamaah::create([
                'jamaah_code'  => $jamaahCode,
                'user_id'      => $user->id,
                'nama_lengkap' => $googleUser->name,
                'source'       => 'google'
            ]);

        } else {

            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'provider'  => 'google'
                ]);
            }
        }

        return $user;
    }

    // ===============================
    // 📧 RESEND EMAIL VERIFICATION
    // ===============================
    public function resendVerification(string $email): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        // 🔥 SUDAH VERIFIED
        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => ['Email sudah diverifikasi']
            ]);
        }

        // 🔥 AMBIL TOKEN TERAKHIR
        $last = EmailVerification::where('email', $email)
            ->orderByDesc('created_at')
            ->first();

        // 🔥 HITUNG SELISIH WAKTU (FIX)
        $diff = $last
            ? $last->created_at->diffInSeconds(now())
            : null;

        // 🔥 RATE LIMIT
        if ($diff !== null && $diff < 60) {
            throw ValidationException::withMessages([
                'email' => ['Tunggu 1 menit sebelum kirim ulang']
            ]);
        }

        // 🔥 HAPUS TOKEN LAMA
        EmailVerification::where('email', $email)->delete();

        // 🔥 BUAT TOKEN BARU
        $token = Str::random(64);

        EmailVerification::create([
            'email'      => $email,
            'token'      => $token,
            'expired_at' => now()->addMinutes(30),
        ]);

        // 🔥 KIRIM EMAIL (QUEUE)
        $this->sendVerificationEmail($user, $token);

        return $user;
    }

}



// namespace App\Services\Auth;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;
// use Illuminate\Support\Facades\Log;
// use App\Models\User;
// use App\Models\Jamaah;
// use App\Models\EmailVerification;
// use App\Services\CodeGeneratorService;
// use Illuminate\Support\Str;
// use App\Mail\VerifyEmailMail;
// use Illuminate\Validation\ValidationException;

// class AuthService
// {
//     protected CodeGeneratorService $code;

//     public function __construct(CodeGeneratorService $code)
//     {
//         $this->code = $code;
//     }

//     public function register(array $data): User
//     {
//         return $this->registerJamaah($data);
//     }

//     // ===============================
//     // ✅ REGISTER JAMAAH (DENGAN VERIFIKASI EMAIL) - LOGIC UTAMA ADA DI SINI               
//     // ===============================

//     public function registerJamaah(array $data): User
//     {
//         $existingUser = User::where('email', $data['email'])->first();

//         // ===============================
//         // 🔥 CASE 1: SUDAH ADA & BELUM VERIFY → RESEND
//         // ===============================
//         if ($existingUser && !$existingUser->email_verified_at) {

//             // 🔥 RATE LIMIT (ANTI SPAM)
//             $last = EmailVerification::where('email', $existingUser->email)
//                 ->latest()
//                 ->first();

//             if ($last && now()->diffInSeconds($last->created_at) < 60) {
//                 throw ValidationException::withMessages([
//                     'email' => ['Tunggu 1 menit sebelum kirim ulang']
//                 ]);
//             }

//             EmailVerification::where('email', $existingUser->email)->delete();

//             $token = Str::random(64);

//             EmailVerification::create([
//                 'email'      => $existingUser->email,
//                 'token'      => $token,
//                 'expired_at' => now()->addMinutes(30),
//             ]);

//             $this->sendVerificationEmail($existingUser, $token);

//             return $existingUser;
//         }

//         // ===============================
//         // 🔥 CASE 2: SUDAH ADA & SUDAH VERIFY → ERROR
//         // ===============================
//         if ($existingUser && $existingUser->email_verified_at) {
//             throw ValidationException::withMessages([
//                 'email' => ['Email sudah terdaftar']
//             ]);
//         }

//         // ===============================
//         // 🔥 CASE 3: USER BARU
//         // ===============================
//         [$user, $token] = DB::transaction(function () use ($data) {

//             $user = User::create([
//                 'name'     => $data['name'],
//                 'email'    => $data['email'],
//                 'password' => Hash::make($data['password'] ?? Str::random(8)),
//             ]);

//             $user->assignRole('JAMAAH');

//             $jamaahCode = $this->code->generate('JMH', 'jamaah');

//             Jamaah::create([
//                 'jamaah_code'   => $jamaahCode,
//                 'user_id'       => $user->id,
//                 'nama_lengkap'  => $data['name'],
//                 'phone'         => $data['phone'] ?? null,
//                 'source'        => 'website',
//             ]);

//             $token = Str::random(64);

//             EmailVerification::create([
//                 'email'      => $user->email,
//                 'token'      => $token,
//                 'expired_at' => now()->addMinutes(30),
//             ]);

//             return [$user, $token];
//         });

//         $this->sendVerificationEmail($user, $token);

//         return $user;
//     }

//     // ===============================
//     // ✅ VERIFY EMAIL
//     // ===============================
//     public function verifyEmail(string $email, string $token): ?User
//     {
//         $verify = EmailVerification::where('email', $email)
//             ->where('token', $token)
//             ->latest()
//             ->first();

//         if (!$verify) return null;

//         if (now()->gt($verify->expired_at)) return null;

//         $user = User::where('email', $email)->first();

//         if (!$user) return null;

//         // 🔥 FIX UTAMA
//         $user->email_verified_at = now();
//         $user->save();
//         $user->refresh();

//         $verify->delete();

//         return $user;
//     }

//     // ===============================
//     // 🔐 GOOGLE LOGIN
//     // ===============================
//     public function googleLogin($googleUser): User
//     {
//         $user = User::where('google_id', $googleUser->id)->first()
//             ?? User::where('email', $googleUser->email)->first();

//         if (!$user) {

//             // ================= CREATE USER
//             $user = User::create([
//                 'name' => $googleUser->name,
//                 'email' => $googleUser->email,
//                 'google_id' => $googleUser->id,
//                 'provider' => 'google',
//                 'email_verified_at' => now(),
//                 'password' => bcrypt(Str::random(16))
//             ]);

//             $user->assignRole('JAMAAH');

//             // ================= CODE
//             $jamaahCode = $this->code->generate(
//                 'JMH',
//                 'jamaah'
//             );

//             // ================= JAMAAH
//             Jamaah::create([
//                 'jamaah_code'  => $jamaahCode,
//                 'user_id'      => $user->id,
//                 'nama_lengkap' => $googleUser->name,
//                 'source'       => 'google'
//             ]);

//         } else {

//             // ================= LINK GOOGLE
//             if (!$user->google_id) {
//                 $user->update([
//                     'google_id' => $googleUser->id,
//                     'provider'  => 'google'
//                 ]);
//             }
//         }

//         return $user;
//     }

//     private function sendVerificationEmail(User $user, string $token): void
//     {
//         $frontend = config('app.frontend_url') ?: 'http://localhost:3000';

//         $link = $frontend
//             . "/verify?email=" . urlencode($user->email)
//             . "&token={$token}";

//         try {
//             Mail::to($user->email)->queue(
//                 new VerifyEmailMail($user->name, $link)
//             );

//             Log::info("Verification email queued: {$user->email}");

//         } catch (\Throwable $e) {
//             Log::error('MAIL ERROR: ' . $e->getMessage());
//         }
//     }

// }