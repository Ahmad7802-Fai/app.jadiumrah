<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Jamaah;

use App\Http\Requests\Api\V1\User\LoginRequest;
use App\Http\Requests\Api\V1\User\RegisterRequest;
use App\Http\Requests\Api\V1\User\ProfileUpdateRequest;

use App\Http\Resources\Api\V1\User\UserResource;

class AuthController extends Controller
{
    // ===============================
    // 🔐 LOGIN
    // ===============================
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        // ❌ USER TIDAK ADA
        if (!$user) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // ❌ PASSWORD SALAH
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        // 🔥 LOAD RELATION AMAN
        $user->loadMissing([
            'branch',
            'agentProfile',
            'jamaahProfile'
        ]);

        // 🔥 HAPUS TOKEN LAMA (optional)
        $user->tokens()->delete();

        // 🔥 CREATE TOKEN
        $token = $user->createToken('auth')->plainTextToken;

        return response()
            ->json([
                'success' => true,
                'data' => new UserResource($user)
            ])
            ->cookie(
                'token',
                $token,
                60 * 24,
                '/',
                null,
                true,   // HTTPS
                true,
                false,
                'Lax'
            );
    }

    // ===============================
    // 📝 REGISTER
    // ===============================
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('JAMAAH');

            Jamaah::create([
                'jamaah_code' => 'JMH-'.date('Ymd').'-'.rand(1000,9999),
                'user_id' => $user->id,
                'nama_lengkap' => $data['name'],
                'phone' => $data['phone'] ?? null,
            ]);

            return $user;
        });

        $token = $user->createToken('auth')->plainTextToken;

        return response()
            ->json([
                'success' => true,
                'data' => new UserResource($user)
            ])
            ->cookie('token', $token, 60 * 24, '/', null, false, true, false, 'Lax');
    }

    // ===============================
    // 👤 ME
    // ===============================
    public function me()
    {
        $user = request()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'data' => null
            ]);
        }

        $user->load(['branch','agentProfile','jamaahProfile']);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    // ===============================
    // ✏️ UPDATE PROFILE
    // ===============================
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    // ===============================
    // 🚪 LOGOUT
    // ===============================
    public function logout()
    {
        request()->user()?->currentAccessToken()?->delete();

        return response()
            ->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ])
            ->cookie('token', '', -1);
    }
}