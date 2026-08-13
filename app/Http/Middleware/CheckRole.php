<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // 🔒 Harus login
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        /**
         * =====================================================
         * ROLE USER (SINGLE SOURCE)
         * =====================================================
         */
        $userRole = strtoupper((string) $user->role);

        $allowedRoles = array_map(
            fn ($r) => strtoupper((string) $r),
            $roles
        );

        /**
         * =====================================================
         * GLOBAL BYPASS (PUSAT)
         * =====================================================
         */
        if (in_array($userRole, ['SUPERADMIN', 'OPERATOR'], true)) {
            return $next($request);
        }

        /**
         * =====================================================
         * ROLE CHECK
         * =====================================================
         */
        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
        }

        return $next($request);
    }
}

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class CheckRole
// {
//     public function handle(Request $request, Closure $next, ...$roles)
//     {
//         $user = Auth::user();

//         if (!$user) {
//             abort(403, 'Silakan login terlebih dahulu.');
//         }

//         // Normalisasi: uppercase semua
//         $userRole = strtoupper($user->role);
//         $allowedRoles = array_map('strtoupper', $roles);

//         /*
//         |--------------------------------------------------------------------------
//         | SUPERADMIN BYPASS — FIX FINAL
//         |--------------------------------------------------------------------------
//         | Superadmin boleh akses semua route tanpa dibatasi.
//         |--------------------------------------------------------------------------
//         */
//         if ($userRole === 'SUPERADMIN') {
//             return $next($request);
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Check role normal
//         |--------------------------------------------------------------------------
//         */
//         if (!in_array($userRole, $allowedRoles)) {
//             abort(403, 'ANDA TIDAK MEMILIKI AKSES.');
//         }

//         return $next($request);
//     }
// }
