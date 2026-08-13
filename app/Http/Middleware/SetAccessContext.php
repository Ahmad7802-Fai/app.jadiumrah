<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetAccessContext
{

    public function handle(Request $request, Closure $next)
    {
        // ===============================
        // JAMAAH
        // ===============================
        if (Auth::guard('jamaah')->check()) {
            $jamaah = Auth::guard('jamaah')->user();

            app()->instance('access.context', [
                'role'      => 'JAMAAH',
                'user_id'   => $jamaah->id,
                'branch_id' => null,
                'agent_id'  => null,
            ]);

            return $next($request);
        }

        // ===============================
        // WEB USER
        // ===============================
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();

            $context = [
                'role'      => strtoupper($user->role),
                'user_id'   => $user->id,
                'branch_id' => null,
                'agent_id'  => null,
            ];

            // 🟢 CABANG (ADMIN)
            if ($user->isCabang()) {
                $context['branch_id'] = $user->branch_id;
            }

            // 🟢 AGENT (SALES + punya agent)
            if ($user->isAgent()) {
                $context['branch_id'] = $user->agent->branch_id;
                $context['agent_id']  = $user->agent->id;
            }

            // 🟢 PUSAT (SUPERADMIN / SALES non-agent)
            if ($user->isPusat()) {
                // tidak set branch_id & agent_id
            }

            app()->instance('access.context', $context);

        }

        return $next($request);
    }

    // public function handle(Request $request, Closure $next)
    // {
    //     /**
    //      * =====================================================
    //      * JAMAAH (END USER)
    //      * =====================================================
    //      */
    //     if (Auth::guard('jamaah')->check()) {
    //         $jamaah = Auth::guard('jamaah')->user();

    //         app()->instance('access.context', [
    //             'role'      => 'JAMAAH',
    //             'user_id'   => $jamaah->id,
    //             'branch_id' => null,
    //             'agent_id'  => null,
    //         ]);

    //         return $next($request);
    //     }

    //     /**
    //      * =====================================================
    //      * WEB USER (PUSAT / CABANG / AGENT)
    //      * =====================================================
    //      */
    //     if (Auth::guard('web')->check()) {
    //         $user = Auth::guard('web')->user();

    //         $context = [
    //             'role'      => strtoupper($user->role), // SUPERADMIN | ADMIN | SALES
    //             'user_id'   => $user->id,
    //             'branch_id' => null,
    //             'agent_id'  => null,
    //         ];

    //         // ADMIN / CABANG
    //         if (in_array($context['role'], ['ADMIN', 'CABANG'])) {
    //             $context['branch_id'] = $user->branch_id;
    //         }

    //         // SALES / AGENT (INI KUNCI)
    //         if ($context['role'] === 'SALES') {
    //             $context['branch_id'] = $user->branch_id;
    //             $context['agent_id']  = optional($user->agent)->id;
    //         }

    //         app()->instance('access.context', $context);
    //     }

    //     return $next($request);
    // }
}

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class SetAccessContext
// {
//     public function handle(Request $request, Closure $next)
//     {
//         // JAMAAH / AGENT
//         if (Auth::guard('jamaah')->check()) {
//             $jamaah = Auth::guard('jamaah')->user();

//             app()->instance('access.context', [
//                 'role'     => 'AGENT',
//                 'agent_id' => $jamaah->id,
//                 'branch_id'=> null,
//             ]);

//             return $next($request);
//         }

//         // WEB (CABANG / PUSAT)
//         if (Auth::guard('web')->check()) {
//             $user = Auth::guard('web')->user();

//             app()->instance('access.context', [
//                 'role'      => strtoupper($user->role),
//                 'branch_id' => $user->branch_id ?? null,
//                 'agent_id'  => null,
//             ]);
//         }

//         return $next($request);
//     }
// }

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class SetAccessContext
// {
//     public function handle(Request $request, Closure $next)
//     {
//         $user = Auth::user();

//         if (!$user) {
//             return $next($request);
//         }

//         // 🔥 ROLE WAJIB UPPERCASE (SAMA DENGAN DB)
//         $context = [
//             'role'      => strtoupper($user->role),
//             'branch_id' => $user->branch_id,
//             'agent_id'  => $user->agent_id ?? null,
//         ];

//         // 🔐 SINGLE SOURCE OF TRUTH
//         app()->instance('access.context', $context);

//         return $next($request);
//     }
// }


// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;

// class SetAccessContext
// {
//     public function handle(Request $request, Closure $next)
//     {
//         if (!auth()->check()) {
//             return $next($request);
//         }

//         $user = auth()->user();

//         app()->instance('access.context', [
//             'role'      => $user->role,
//             'branch_id' => $user->branch_id,
//             'agent_id'  => optional($user->agent)->id,
//         ]);

//         return $next($request);
//     }
// }
