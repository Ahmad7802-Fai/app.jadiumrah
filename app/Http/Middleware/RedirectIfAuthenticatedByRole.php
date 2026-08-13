<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{
    public function handle(Request $request, Closure $next)
    {
        // 🔒 HANYA INTERCEPT HALAMAN LOGIN
        if ($request->is('login')) {

            // Jamaah
            if (Auth::guard('jamaah')->check()) {
                return redirect()->route('jamaah.dashboard');
            }

            // User internal
            if (Auth::check()) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
