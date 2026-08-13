<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDuplicateRegistration
{
    public function handle(Request $request, Closure $next)
    {
        // 🚫 Sudah submit dalam session ini
        if (session()->get('registration_lock') === true) {
            abort(429, 'Pendaftaran sedang diproses. Silakan tunggu.');
        }

        // 🔒 Pasang lock sebelum controller
        session()->put('registration_lock', true);

        return $next($request);
    }
}
