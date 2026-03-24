<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\Users\ProfileService;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $service
    ) {}

    public function show(Request $request)
    {
        $user = $request->user()->load([
            'branch',
            'roles',
            'agentProfile',
            'jamaahProfile',
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | USERS
            |--------------------------------------------------------------------------
            */
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            /*
            |--------------------------------------------------------------------------
            | AGENT
            |--------------------------------------------------------------------------
            */
            'nama' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',

            /*
            |--------------------------------------------------------------------------
            | JAMAAH
            |--------------------------------------------------------------------------
            */
            'nama_lengkap' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
        ]);

        $user = $this->service->update($user, $data);

        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $this->service->changePassword($user, $data);

        return response()->json([
            'message' => 'Password berhasil diperbarui',
        ]);
    }
}