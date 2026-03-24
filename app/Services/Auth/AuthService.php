<?php 

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerJamaah(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            $user->assignRole('JAMAAH');

            Jamaah::create([
                'user_id' => $user->id,
                'nama_lengkap' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'source' => 'website'
            ]);

            return $user;
        });
    }
}