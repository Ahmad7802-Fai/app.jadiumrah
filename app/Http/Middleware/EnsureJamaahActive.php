<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureJamaahActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('jamaah')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            return view('jamaah.waiting');
        }

        return $next($request);
    }

}
