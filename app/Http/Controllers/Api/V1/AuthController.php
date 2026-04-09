<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Api\V1\User\LoginRequest;
use App\Http\Requests\Api\V1\User\RegisterRequest;
use App\Http\Requests\Api\V1\User\VerifyEmailRequest;
use App\Http\Requests\Api\V1\User\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\User\ResetPasswordRequest;
use App\Http\Resources\Api\V1\User\UserResource;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    // ================= REGISTER
    public function register(RegisterRequest $request)
    {
        $this->service->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cek email untuk verifikasi'
        ]);
    }

    // ================= VERIFY EMAIL
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $this->service->verifyEmail(
            $request->email,
            $request->token
        );

        // ❌ TOKEN INVALID / EXPIRED
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid / expired'
            ], 400);
        }

        // 🔥 AUTO LOGIN (GENERATE TOKEN)
        $user->tokens()->delete();
        $token = $user->createToken('auth')->plainTextToken;

        // ✅ RESPONSE FINAL
        return response()->json([
            'success' => true,
            'message' => 'Email verified',
            'token' => $token,
            'data' => new UserResource($user)
        ]);
    }

    // ================= LOGIN EMAIL
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'message' => 'Email / password salah'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email belum diverifikasi'
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token, // 🔥 INI KUNCI
            'data' => new UserResource($user)
        ]);
    }

    // ================= GOOGLE
    public function redirectGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogle()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = $this->service->googleLogin($googleUser);

        $user->tokens()->delete();
        $token = $user->createToken('auth')->plainTextToken;

        return redirect(config('app.frontend_url'))
            ->cookie(AuthHelper::make($token));
    }

    // ================= FORGOT PASSWORD
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        Password::sendResetLink($request->only('email'));

        return response()->json([
            'success' => true,
            'message' => 'Link reset dikirim'
        ]);
    }

    // ================= RESET PASSWORD
    public function resetPassword(ResetPasswordRequest $request)
    {
        Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password)
                ]);
            }
        );

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }

    // ================= ME (GET USER LOGIN)
    public function me()
    {
        $user = request()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    // ================= LOGOUT
    public function logout()
    {
        request()->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true
        ]);
    }

}
