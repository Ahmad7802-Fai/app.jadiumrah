<?php

namespace App\Http\Middleware;

use Closure;

class InjectSanctumToken
{
    public function handle($request, Closure $next)
    {
        if ($request->cookie('token')) {
            $request->headers->set(
                'Authorization',
                'Bearer '.$request->cookie('token')
            );
        }

        return $next($request);
    }
}
