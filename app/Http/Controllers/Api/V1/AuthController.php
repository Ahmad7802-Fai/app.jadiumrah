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

        if (!$user) {
            return response()->json([
                'message' => 'Token invalid / expired'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verified'
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
            'data' => new UserResource($user)
        ])->cookie(AuthHelper::make($token));
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

        return redirect(env('FRONTEND_URL'))
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

    // ================= LOGOUT
    public function logout()
    {
        request()->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true
        ])->cookie(AuthHelper::forget());
    }
}

// namespace App\Http\Controllers\Api\V1;

// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash;

// use App\Models\User;
// use App\Models\Jamaah;
// use App\Helpers\AuthHelper;

// use App\Http\Requests\Api\V1\User\LoginRequest;
// use App\Http\Requests\Api\V1\User\RegisterRequest;
// use App\Http\Requests\Api\V1\User\ProfileUpdateRequest;

// use App\Http\Resources\Api\V1\User\UserResource;

// class AuthController extends Controller
// {
//     // ===============================
//     // 🔐 LOGIN
//     // ===============================
//     public function login(LoginRequest $request)
//     {
//         $user = User::where('email', $request->email)->first();

//         if (!$user || !Hash::check($request->password, $user->password)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Email atau password salah'
//             ], 401);
//         }

//         $user->loadMissing([
//             'branch',
//             'agentProfile',
//             'jamaahProfile'
//         ]);

//         $user->tokens()->delete();

//         $token = $user->createToken('auth')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'Login berhasil',
//             'data' => new UserResource($user)
//         ])->cookie(AuthHelper::make($token));
//     }

//     // ===============================
//     // 📝 REGISTER
//     // ===============================
//     public function register(RegisterRequest $request)
//     {
//         $data = $request->validated();

//         $user = DB::transaction(function () use ($data) {

//             $user = User::create([
//                 'name' => $data['name'],
//                 'email' => $data['email'],
//                 'password' => Hash::make($data['password']),
//             ]);

//             $user->assignRole('JAMAAH');

//             Jamaah::create([
//                 'jamaah_code' => 'JMH-' . date('Ymd') . '-' . rand(1000,9999),
//                 'user_id' => $user->id,
//                 'nama_lengkap' => $data['name'],
//                 'phone' => $data['phone'] ?? null,
//             ]);

//             return $user;
//         });

//         $user->loadMissing([
//             'branch',
//             'agentProfile',
//             'jamaahProfile'
//         ]);

//         $token = $user->createToken('auth')->plainTextToken;

//         return response()->json([
//             'success' => true,
//             'message' => 'Register berhasil',
//             'data' => new UserResource($user)
//         ])->cookie(AuthHelper::make($token));
//     }

//     // ===============================
//     // 👤 ME
//     // ===============================
//     public function me()
//     {
//         $user = request()->user();

//         if (!$user) {
//             return response()->json([
//                 'success' => false,
//                 'data' => null
//             ], 401);
//         }

//         $user->loadMissing([
//             'branch',
//             'agentProfile',
//             'jamaahProfile'
//         ]);

//         return response()->json([
//             'success' => true,
//             'data' => new UserResource($user)
//         ]);
//     }

//     // ===============================
//     // ✏️ UPDATE PROFILE
//     // ===============================
//     public function update(ProfileUpdateRequest $request)
//     {
//         $user = $request->user();

//         $user->update($request->validated());

//         $user->loadMissing([
//             'branch',
//             'agentProfile',
//             'jamaahProfile'
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Profile berhasil diupdate',
//             'data' => new UserResource($user)
//         ]);
//     }

//     // ===============================
//     // 🚪 LOGOUT
//     // ===============================
//     public function logout()
//     {
//         if (request()->user()) {
//             request()->user()->currentAccessToken()?->delete();
//         }

//         return response()->json([
//             'success' => true,
//             'message' => 'Logout berhasil'
//         ])->cookie(AuthHelper::forget());
//     }
// }