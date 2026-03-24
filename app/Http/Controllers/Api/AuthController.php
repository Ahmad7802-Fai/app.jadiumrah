<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Jamaah;

use App\Support\UserRoleResolver;

class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | REGISTER JAMAAH
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {

        $data = $request->validate([

            'name' => ['required','string','max:255'],

            'email' => ['required','email','unique:users,email'],

            'password' => ['required','min:6','confirmed'],

            'phone' => ['nullable','string','max:20'],

        ]);

        $user = DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | CREATE USER
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'name' => $data['name'],

                'email' => $data['email'],

                'password' => Hash::make($data['password'])

            ]);

            /*
            |--------------------------------------------------------------------------
            | ASSIGN ROLE JAMAAH
            |--------------------------------------------------------------------------
            */

            $user->assignRole('JAMAAH');


            /*
            |--------------------------------------------------------------------------
            | CREATE JAMAAH PROFILE
            |--------------------------------------------------------------------------
            */

            Jamaah::create([

                'jamaah_code' => 'JMH-'.date('Ymd').'-'.rand(1000,9999),

                'user_id' => $user->id,

                'source' => 'website',

                'nama_lengkap' => $data['name'],

                'phone' => $data['phone'] ?? null

            ]);

            return $user;

        });

        /*
        |--------------------------------------------------------------------------
        | CREATE TOKEN
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken(
            $request->userAgent() ?? 'umrahcore-register'
        )->plainTextToken;

        return response()->json([

            'message' => 'Register berhasil',

            'token' => $token,

            'user' => $this->userPayload($user)

        ],201);

    }



    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {

        $credentials = $request->validate([

            'email' => ['required','email'],

            'password' => ['required'],

        ]);

        /*
        |--------------------------------------------------------------------------
        | ATTEMPT LOGIN
        |--------------------------------------------------------------------------
        */

        if(!Auth::attempt($credentials)){

            return response()->json([
                'message' => 'Email atau password salah'
            ],401);

        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | LOAD RELATIONS
        |--------------------------------------------------------------------------
        */

        $user->load([
            'branch',
            'agentProfile',
            'jamaahProfile'
        ]);

        /*
        |--------------------------------------------------------------------------
        | CHECK ACTIVE
        |--------------------------------------------------------------------------
        */

        if(isset($user->is_active) && !$user->is_active){

            return response()->json([
                'message' => 'Akun dinonaktifkan'
            ],403);

        }

        /*
        |--------------------------------------------------------------------------
        | DELETE OLD TOKENS
        |--------------------------------------------------------------------------
        */

        $user->tokens()->delete();

        /*
        |--------------------------------------------------------------------------
        | CREATE TOKEN
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken(
            $request->userAgent() ?? 'umrahcore-api'
        )->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'token' => $token,

            'user' => $this->userPayload($user)

        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {

        $request->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | CURRENT USER
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {

        $user = $request->user();

        if(!$user){

            return response()->json([
                'user' => null
            ]);

        }

        $user->load([
            'branch',
            'agentProfile',
            'jamaahProfile'
        ]);

        return response()->json([
            'user' => $this->userPayload($user)
        ]);

    }



    /*
    |--------------------------------------------------------------------------
    | USER PAYLOAD (CLEAN RESPONSE)
    |--------------------------------------------------------------------------
    */

    private function userPayload($user): array
    {

        $role = UserRoleResolver::resolve($user);

        return [

            'id' => $user->id,

            'name' => $user->name,

            'email' => $user->email,

            /*
            |--------------------------------------------------------------------------
            | ROLE UNTUK FRONTEND
            |--------------------------------------------------------------------------
            */

            'role' => $role,

            /*
            |--------------------------------------------------------------------------
            | ALL ROLES
            |--------------------------------------------------------------------------
            */

            'roles' => $user->getRoleNames()->values(),

            /*
            |--------------------------------------------------------------------------
            | BRANCH
            |--------------------------------------------------------------------------
            */

            'branch_id' => $user->branch_id,

            'branch' => $user->branch?->only([
                'id',
                'name'
            ]),

            /*
            |--------------------------------------------------------------------------
            | PROFILE TYPE
            |--------------------------------------------------------------------------
            */

            'profile_type' => $role === 'agent'
                ? 'agent'
                : ($role === 'jamaah' ? 'jamaah' : 'staff'),

            /*
            |--------------------------------------------------------------------------
            | AGENT PROFILE
            |--------------------------------------------------------------------------
            */

            'agent_profile' => $user->agentProfile?->only([
                'id',
                'nama',
                'kode_agent',
                'phone'
            ]),

            /*
            |--------------------------------------------------------------------------
            | JAMAAH PROFILE
            |--------------------------------------------------------------------------
            */

            'jamaah_profile' => $user->jamaahProfile?->only([
                'id',
                'jamaah_code',
                'nama_lengkap',
                'phone'
            ]),

        ];

    }

}