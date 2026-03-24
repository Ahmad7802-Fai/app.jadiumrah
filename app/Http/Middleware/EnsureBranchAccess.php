<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        // Jika route ada branch parameter
        if ($request->route('branch')) {

            $branch = $request->route('branch');

            if (
                !$user->hasAnyRole(['SUPERADMIN','ADMIN_PUSAT']) &&
                $user->branch_id !== $branch->id
            ) {
                abort(403, 'Unauthorized branch access');
            }
        }

        return $next($request);
    }

}
