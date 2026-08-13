<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\PaketUmrah;
use App\Services\Dashboard\AgentDashboardService;

class DashboardController extends Controller
{
    public function index(AgentDashboardService $dashboardService)
    {
        $user = auth()->user();

        /* ======================
           AGENT
        ====================== */
        $agent = Agent::where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        /* ======================
           KPI DASHBOARD
        ====================== */
        $kpi = $dashboardService
            ->forAgent($agent->id)
            ->kpi();

        /* ======================
           PAKET & REFERRAL LINK
        ====================== */
        $paketAktif = PaketUmrah::where('is_active', true)
            ->where('status', 'Aktif')
            ->orderBy('tglberangkat')
            ->get();

        $agentKey = $agent->slug ?: $agent->kode_agent; // 🔥 FIX UTAMA

        $links = $paketAktif->map(fn ($paket) => [
            'paket' => $paket,
            'link'  => route('paket.umrah.by-agent', [
                'agent' => $agentKey,
                'slug'  => $paket->slug,
            ]),
        ]);

        /* ======================
           VIEW
        ====================== */
        return view('agent.dashboard.index', [
            'agent' => $agent,
            'kpi'   => $kpi,
            'links' => $links,
        ]);
    }
}

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\PaketUmrah;
// use App\Services\Dashboard\AgentDashboardService;
// use App\Services\Komisi\AgentKomisiDashboardService;

// class DashboardController extends Controller
// {
//     public function index(
//         AgentDashboardService $dashboardService,
//         AgentKomisiDashboardService $komisiService
//     ) {
//         $user = auth()->user();

//         /* ======================
//            AGENT
//         ====================== */
//         $agent = Agent::where('user_id', $user->id)
//             ->where('is_active', true)
//             ->firstOrFail();

//         /* ======================
//            KPI DASHBOARD
//         ====================== */
//         $dashboard = new AgentDashboardService(
//             $agent->id,
//             $komisiService
//         );

//         $kpi = $dashboard->kpi();

//         /* ======================
//            PAKET & REFERRAL LINK
//         ====================== */
//         $paketAktif = PaketUmrah::where('is_active', true)
//             ->where('status', 'Aktif')
//             ->orderBy('tglberangkat')
//             ->get();

//         $links = $paketAktif->map(fn ($paket) => [
//             'paket' => $paket,
//             'link'  => route('paket.umrah.show', [
//                 'slug' => $paket->slug,
//                 'ref'  => $agent->kode_agent,
//             ]),
//         ]);

//         /* ======================
//            VIEW
//         ====================== */
//         return view('agent.dashboard.index', [
//             'agent' => $agent,
//             'kpi'   => $kpi,
//             'links' => $links,
//         ]);
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\PaketUmrah;
// use App\Services\Dashboard\AgentDashboard;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $user = auth()->user();

//         /* ======================
//            AGENT
//         ====================== */
//         $agent = Agent::where('user_id', $user->id)
//             ->where('is_active', true)
//             ->firstOrFail();

//         /* ======================
//            KPI DASHBOARD
//         ====================== */
//         $dashboard = new AgentDashboard($agent->id);
//         $kpi = $dashboard->kpi();

//         /* ======================
//            PAKET & REFERRAL LINK
//         ====================== */
//         $paketAktif = PaketUmrah::where('is_active', true)
//             ->where('status', 'Aktif')
//             ->orderBy('tglberangkat')
//             ->get();

//         $links = $paketAktif->map(fn ($paket) => [
//             'paket' => $paket,
//             'link'  => route('paket.umrah.show', [
//                 'slug' => $paket->slug,
//                 'ref'  => $agent->kode_agent,
//             ]),
//         ]);

//         /* ======================
//            VIEW
//         ====================== */
//         return view('agent.dashboard.index', [
//             'agent' => $agent,
//             'kpi'   => $kpi,
//             'links' => $links,
//         ]);
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\PaketUmrah;
// use App\Services\Dashboard\AgentDashboard;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $user = auth()->user();

//         // 🔐 Ambil agent
//         $agent = Agent::where('user_id', $user->id)
//             ->where('is_active', 1)
//             ->firstOrFail();

//         // 📊 Dashboard service
//         $dashboard = new AgentDashboard($agent->id);

//         // 📦 Paket aktif
//         $paket = PaketUmrah::where('is_active', 1)
//             ->where('status', 'Aktif')
//             ->orderBy('tglberangkat')
//             ->get();

//         // 🔗 Generate link referral
// $links = $paket->map(function ($p) use ($agent) {
//     return [
//         'paket' => $p,
//         'link'  => route('paket.umrah.show', [
//             'slug' => $p->slug,
//             'ref'  => $agent->kode_agent
//         ]),
//     ];
// });



//         $dashboard = new AgentDashboard($agent->id);

//         return view('agent.dashboard.index', [
//             'agent' => $agent,
//             'kpi'   => $dashboard->kpi(),
//             'links' => $links,
//         ]);

//     }
// }


// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\PaketUmrah;
// use App\Models\Agent;
// use App\Services\Dashboard\AgentDashboard;
// class DashboardController extends Controller
// {

// public function index()
// {
//     $agentId = auth()->user()->agent->id;

//     $dashboard = new AgentDashboard($agentId);

//     return view('agent.dashboard.index', [
//         'cards'        => $dashboard->cards(),
//         'recentJamaah' => $dashboard->recentJamaah(),
//     ]);
// }
// }


// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Services\Dashboard\AgentDashboardService;

// class DashboardController extends Controller
// {
//     public function __construct(
//         protected AgentDashboardService $dashboardService
//     ) {}

//     public function index()
//     {
//         abort_unless(auth()->user()->isAgent(), 403);

//         $kpi = $this->dashboardService
//             ->getKpi(auth()->id());

//         return view('agent.dashboard.index', compact('kpi'));
//     }
// }
