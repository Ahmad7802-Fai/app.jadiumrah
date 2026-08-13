<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /* ======================================================
     | PROFILE PAGE
     | - Data akun login → jamaah_users
     | - Data jamaah → READ ONLY
     ====================================================== */
    public function index()
    {
        $user   = auth('jamaah')->user();   // jamaah_users
        $jamaah = $user->jamaah;            // hanya untuk display

        return view('jamaah.profile.index', compact('user', 'jamaah'));
    }

    /* ======================================================
     | UPDATE PROFILE (AKUN LOGIN)
     | - phone → jamaah_users
     | - nama_lengkap → jamaah
     ====================================================== */
    public function update(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'phone'        => 'required|string|max:20',
        ]);

        $user   = auth('jamaah')->user();   // jamaah_users
        $jamaah = $user->jamaah;

        // 🔄 Update akun login
        $user->update([
            'phone' => trim($request->phone),
        ]);

        // 🔄 Update data jamaah (nama saja)
        $jamaah->update([
            'nama_lengkap' => trim($request->nama_lengkap),
        ]);

        // 🔐 Security log
        security_log(
            'profile_updated',
            'Profil akun jamaah diperbarui'
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /* ======================================================
     | UPDATE PASSWORD (JAMAAH USERS)
     ====================================================== */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth('jamaah')->user();

        // ❌ Password lama salah
        if (!Hash::check($request->current_password, $user->password)) {

            security_log(
                'password_change_failed',
                'Password lama tidak sesuai'
            );

            throw ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }

        // ✅ Update password
        $user->update([
            'password'            => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        // 🔐 Security log
        security_log(
            'password_changed',
            'User mengganti password'
        );

        /* ======================================================
         | FORCE LOGOUT ALL SESSION
         ====================================================== */
        Auth::guard('jamaah')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Password berhasil diperbarui. Silakan login kembali.');
    }
}
