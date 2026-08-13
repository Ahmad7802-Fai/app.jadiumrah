<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * =====================================================
     * LOGIN — SINGLE ENTRY, MULTI ROLE (STABIL)
     * =====================================================
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ LOGIN JAMAAH
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('jamaah')->attempt([
            $loginField => $request->login,
            'password'  => $request->password,
            'is_active' => 1,
        ])) {

            // 🔐 Regenerate & bersihkan redirect lama
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            // Security marker
            session([
                'password_confirmed_at' => now()->timestamp,
            ]);

            return redirect()->route('jamaah.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ LOGIN INTERNAL (WEB)
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('web')->attempt([
            'email'     => $request->login,
            'password'  => $request->password,
            'is_active' => 1,
        ])) {

            // 🔐 Regenerate & bersihkan redirect lama
            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            $user = Auth::guard('web')->user();

            /*
            |--------------------------------------------------------------------------
            | 🎯 PENENTUAN DASHBOARD (SATU-SATUNYA TEMPAT)
            |--------------------------------------------------------------------------
            */

            // AGENT (SALES + agent_id)
            if ($user->isAgent()) {
                return redirect()->route('agent.dashboard');
            }


            // ADMIN CABANG
            if ($user->role === 'ADMIN' && !empty($user->branch_id)) {
                return redirect()->route('cabang.dashboard');
            }

            // PUSAT (ADMIN PUSAT, SALES PUSAT, SUPERADMIN, DLL)
            return redirect()->route('dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ GAGAL LOGIN
        |--------------------------------------------------------------------------
        */
        return back()->withErrors([
            'login' => 'Email / No HP atau password salah',
        ]);
    }

    /**
     * =====================================================
     * LOGOUT — BERSIH SEMUA SESSION
     * =====================================================
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('jamaah')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
