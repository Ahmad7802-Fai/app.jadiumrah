<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsFresh
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ HANYA untuk jamaah login
        if (!Auth::guard('jamaah')->check()) {
            return $next($request);
        }

        // ✅ JANGAN blokir TABUNGAN
        if ($request->is('jamaah/tabungan*')) {
            return $next($request);
        }

        $user = Auth::guard('jamaah')->user();

        /**
         * ======================================
         * SET SESSION LOGIN TIME (ONCE)
         * ======================================
         */
        if (!session()->has('password_confirmed_at')) {
            session([
                'password_confirmed_at' => now()->timestamp,
            ]);

            return $next($request);
        }

        /**
         * ======================================
         * FORCE LOGOUT JIKA PASSWORD DIUBAH
         * ======================================
         */
        if ($user->password_changed_at) {
            $passwordChangedAt = $user->password_changed_at->timestamp;
            $confirmedAt       = session('password_confirmed_at');

            if ($passwordChangedAt > $confirmedAt) {
                Auth::guard('jamaah')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->withErrors([
                    'login' => 'Password Anda telah berubah. Silakan login kembali.',
                ]);
            }
        }

        return $next($request);
    }
}
