<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\PaketUmrah;
use App\Models\Agent;
use Illuminate\Http\Request;

class PaketUmrahController extends Controller
{
    public function show(Request $request, $param1, $param2 = null)
    {

        /**
         * ============================
         * NORMALISASI PARAMETER
         * ============================
         * /paket-umrah/{slug}
         * /{agent}/{slug}
         */
        if ($param2) {
            $agentSlug = $param1;
            $slug      = $param2;
        } else {
            $agentSlug = null;
            $slug      = $param1;
        }

        /**
         * ============================
         * AMBIL PAKET
         * ============================
         */
        $paket = PaketUmrah::where('slug', $slug)
            ->where('status', 'Aktif')
            ->first();

        if (!$paket) {
            abort(404, 'Paket tidak ditemukan');
        }

        /**
         * ============================
         * CAPTURE REFERRAL (BACKUP)
         * ============================
         */
        if ($agentSlug && !session()->has('referral')) {
            $agent = Agent::where('slug', $agentSlug)
                ->orWhere('kode_agent', $agentSlug)
                ->where('is_active', 1)
                ->first();

            if ($agent) {
                session([
                    'referral' => [
                        'agent_id'   => $agent->id,
                        'branch_id'  => $agent->branch_id,
                        'kode_agent' => $agent->kode_agent,
                        'slug'       => $agent->slug,
                        'captured_at'=> now(),
                    ]
                ]);
            }
        }

        return view('website.paket.show', [
            'paket'    => $paket,
            'referral' => session('referral'),
        ]);
    }
}

// namespace App\Http\Controllers\Website;

// use App\Http\Controllers\Controller;
// use App\Models\PaketUmrah;
// use Illuminate\Http\Request;

// class PaketUmrahController extends Controller
// {
//     public function show(Request $request, string $slug)
//     {
//         // 🔎 Ambil paket TANPA SYARAT DULU
//         $paket = PaketUmrah::where('slug', $slug)->first();

//         if (!$paket) {
//             abort(404, 'Paket tidak ditemukan');
//         }

//         // 🧠 Referral (kalau ada)
//         $referral = session('referral');

//         return view('website.paket.show', [
//             'paket'    => $paket,
//             'referral' => $referral,
//         ]);
//     }
// }
