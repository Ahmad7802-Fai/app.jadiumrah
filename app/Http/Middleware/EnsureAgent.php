<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAgent
{

    public function handle(Request $request, Closure $next)
    {
        $context = app('access.context');

        // Jika punya agent_id = AGENT
        if (!empty($context['agent_id'])) {

            // 🔒 HANYA cegah akses ke dashboard pusat
            if ($request->routeIs('dashboard')) {
                return redirect()->route('agent.dashboard');
            }
        }

        return $next($request);
    }
}
