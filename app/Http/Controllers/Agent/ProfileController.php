<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        $user  = auth()->user();

        // RELASI SESUAI DB
        $agent = Agent::where('user_id', $user->id)->firstOrFail();

        return view('agent.profile.edit', compact('agent'));
    }

    public function update(Request $request)
    {
        $user  = auth()->user();
        $agent = Agent::where('user_id', $user->id)->firstOrFail();

        /* ===============================
         | VALIDATION
         =============================== */
        $data = $request->validate([
            // AGENT PROFILE
            'nama'  => ['required','string','max:100'],
            'phone' => ['required','string','max:20'],

            // BANK
            'bank_name'           => ['required','string','max:100'],
            'bank_account_number' => ['required','string','max:50'],
            'bank_account_name'   => ['required','string','max:100'],

            // PASSWORD (OPSIONAL)
            'current_password' => ['nullable','string'],
            'password'         => ['nullable','string','min:8','confirmed'],
        ]);

        /* ===============================
         | UPDATE AGENT
         =============================== */
        $agent->update([
            'nama'                => $data['nama'],
            'phone'               => $data['phone'],
            'bank_name'           => $data['bank_name'],
            'bank_account_number' => $data['bank_account_number'],
            'bank_account_name'   => $data['bank_account_name'],
        ]);

        /* ===============================
         | UPDATE PASSWORD (USERS)
         =============================== */
        if (!empty($data['password'])) {

            if (empty($data['current_password']) ||
                !Hash::check($data['current_password'], $user->password)) {

                throw ValidationException::withMessages([
                    'current_password' => 'Password lama tidak sesuai.',
                ]);
            }

            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return back()->with('success', 'Profil agent berhasil diperbarui.');
    }
}
