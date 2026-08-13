<?php

// namespace App\Http\Middleware;

// use Illuminate\Auth\Middleware\Authenticate as Middleware;
// use Illuminate\Http\Request;

// class Authenticate extends Middleware
// {
//     protected function redirectTo(Request $request): ?string
//     {
//         if ($request->expectsJson()) {
//             return null;
//         }

//         // 🔥 KHUSUS JAMAAH
//         if ($request->is('jamaah') || $request->is('jamaah/*')) {
//             return route('jamaah.login');
//         }

//         // DEFAULT ADMIN / WEB
//         return route('login');
//     }
// }


namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
