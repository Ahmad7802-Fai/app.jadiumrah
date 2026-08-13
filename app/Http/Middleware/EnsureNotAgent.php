<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureNotAgent
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

        return $next($request);
    }
}
