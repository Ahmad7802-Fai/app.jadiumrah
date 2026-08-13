<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Agent;
use App\Models\PaketUmrah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CaptureReferral
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * =====================================================
         * 1️⃣ AMBIL PARAMETER DARI URL
         * =====================================================
         */
        $agentParam = $request->route('agent') ?? $request->query('ref');
        $paketParam = $request->route('slug');

        /**
         * =====================================================
         * 2️⃣ STOP JIKA:
         * - agent tidak ada
         * - paket tidak ada
         * =====================================================
         */
        if (! $agentParam || ! $paketParam) {
            return $next($request);
        }

        /**
         * =====================================================
         * 3️⃣ RESOLVE AGENT (kode / slug)
         * =====================================================
         */
        $agent = Agent::where('is_active', 1)
            ->where(function ($q) use ($agentParam) {
                $q->where('kode_agent', $agentParam)
                  ->orWhere('slug', $agentParam);
            })
            ->first();

        if (! $agent) {
            return $next($request);
        }

        /**
         * =====================================================
         * 4️⃣ RESOLVE PAKET (HARUS AKTIF)
         * =====================================================
         */
        $paket = PaketUmrah::where('slug', $paketParam)
            ->where('status', 'Aktif')
            ->first();

        if (! $paket) {
            return $next($request);
        }

        /**
         * =====================================================
         * 5️⃣ BUILD PAYLOAD REFERRAL
         * =====================================================
         */
        $payload = [
            'agent_id'   => $agent->id,
            'branch_id'  => $agent->branch_id,
            'kode_agent' => $agent->kode_agent,
            'agent_slug' => $agent->slug,

            'paket_id'   => $paket->id,
            'paket_slug' => $paket->slug,

            'captured_at'=> now()->toDateTimeString(),
        ];

        /**
         * =====================================================
         * 6️⃣ SIMPAN KE SESSION (DIPAKAI SERVICE)
         * =====================================================
         */
        session(['referral' => $payload]);

        /**
         * =====================================================
         * 7️⃣ SIMPAN KE COOKIE (30 HARI, LINTAS SUBDOMAIN)
         * =====================================================
         */
        Cookie::queue(
            Cookie::make(
                'ref_agent',
                json_encode($payload),
                60 * 24 * 30,        // 30 hari
                '/',
                '.hasantour.co.id', // lintas subdomain
                false,              // secure → true kalau https
                true                // httpOnly
            )
        );

        return $next($request);
    }
}

// namespace App\Http\Middleware;

// use Closure;
// use App\Models\Agent;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cookie;

// class CaptureReferral
// {
//     public function handle(Request $request, Closure $next)
//     {
//         /**
//          * =====================================================
//          * 1️⃣ Ambil referral dari URL
//          * Support:
//          * - ?ref=AGT-JKT-01-001
//          * - ?ref=ahmad
//          * - /by/{agent}
//          * =====================================================
//          */
//         $refFromQuery = $request->query('ref');
//         $refFromPath  = $request->route('agent'); // optional route param

//         $ref = $refFromQuery ?: $refFromPath;

//         /**
//          * =====================================================
//          * 2️⃣ STOP kalau:
//          * - Tidak ada ref
//          * - Sudah ada cookie referral
//          * =====================================================
//          */
//         if (!$ref || $request->cookie('ref_agent')) {
//             return $next($request);
//         }

//         /**
//          * =====================================================
//          * 3️⃣ Cari agent (kode_agent ATAU slug)
//          * =====================================================
//          */
//         $agent = Agent::query()
//             ->where('is_active', 1)
//             ->where(function ($q) use ($ref) {
//                 $q->where('kode_agent', $ref)
//                   ->orWhere('slug', $ref);
//             })
//             ->first();

//         if (!$agent) {
//             return $next($request);
//         }

//         /**
//          * =====================================================
//          * 4️⃣ Simpan referral ke COOKIE (lintas subdomain)
//          * =====================================================
//          */
//         $payload = [
//             'agent_id'    => $agent->id,
//             'branch_id'   => $agent->branch_id,
//             'kode_agent'  => $agent->kode_agent,
//             'slug'        => $agent->slug,
//             'captured_at' => now()->toDateTimeString(),
//         ];

//         Cookie::queue(
//             Cookie::make(
//                 'ref_agent',
//                 json_encode($payload),
//                 60 * 24 * 30,     // 30 hari
//                 '/',              // path
//                 '.hasantour.co.id', // 🔥 lintas subdomain
//                 false,            // secure (true kalau HTTPS)
//                 true              // httpOnly
//             )
//         );

//         return $next($request);
//     }
// }

// namespace App\Http\Middleware;

// use Closure;
// use App\Models\Agent;

// class CaptureReferral
// {
//     public function handle($request, Closure $next)
//     {
//         $ref = $request->query('ref');

//         if ($ref && !session()->has('referral')) {

//             $agent = Agent::where('kode_agent', $ref)
//                 ->where('is_active', 1)
//                 ->first();

//             if ($agent) {
//                 session([
//                     'referral' => [
//                         'agent_id'   => $agent->id,
//                         'branch_id'  => $agent->branch_id,
//                         'kode_agent' => $agent->kode_agent,
//                         'captured_at' => now(),
//                     ]
//                 ]);
//             }
//         }

//         return $next($request);
//     }
// }
