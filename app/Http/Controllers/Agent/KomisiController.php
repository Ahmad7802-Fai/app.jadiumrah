<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Services\Komisi\AgentKomisiDashboardService;

class KomisiController extends Controller
{
    protected int $agentId;

    public function __construct(
        protected AgentKomisiDashboardService $dashboardService
    ) {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            abort_unless($user && $user->role === 'SALES', 403);

            $this->agentId = Agent::where('user_id', $user->id)->value('id');
            abort_unless($this->agentId, 403);

            return $next($request);
        });
    }

    public function index()
    {
        $data = $this->dashboardService
            ->getDashboardData($this->agentId);

        return view('agent.komisi.index', $data);
    }
}


// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Services\Komisi\AgentKomisiDashboardService;

// class KomisiController extends Controller
// {
//     protected int $agentId;

//     public function __construct(
//         protected AgentKomisiDashboardService $dashboardService
//     ) {
//         $this->middleware(function ($request, $next) {

//             $user = auth()->user();
//             abort_unless($user && $user->role === 'SALES', 403);

//             $this->agentId = Agent::where('user_id', $user->id)->value('id');
//             abort_unless($this->agentId, 403);

//             return $next($request);
//         });
//     }

//     public function index()
//     {
//         $data = $this->dashboardService->getDashboardData($this->agentId);

//         return view('agent.komisi.index', $data);
//     }
// }


// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\KomisiLogs;
// use App\Services\Komisi\AgentKomisiDashboardService;

// class KomisiController extends Controller
// {
//     protected int $agentId;

//     public function __construct(
//         protected AgentKomisiDashboardService $dashboardService
//     ) {
//         $this->middleware(function ($request, $next) {

//             $user = auth()->user();
//             abort_unless($user && $user->role === 'SALES', 403);

//             $this->agentId = Agent::where('user_id', $user->id)->value('id');
//             abort_unless($this->agentId, 403);

//             return $next($request);
//         });
//     }

//     public function index()
//     {
//         $komisi = KomisiLogs::with('jamaah')
//             ->where('agent_id', $this->agentId)
//             ->latest()
//             ->paginate(15);

//         $summary = [
//             'total' => KomisiLogs::where('agent_id', $this->agentId)
//                 ->sum('komisi_nominal'),

//             'pending' => KomisiLogs::where('agent_id', $this->agentId)
//                 ->where('status', 'pending')
//                 ->sum('komisi_nominal'),

//             'approved' => KomisiLogs::where('agent_id', $this->agentId)
//                 ->where('status', 'approved')
//                 ->sum('komisi_nominal'),

//             'paid' => KomisiLogs::where('agent_id', $this->agentId)
//                 ->where('status', 'paid')
//                 ->sum('komisi_nominal'),
//         ];

//         return view('agent.komisi.index', compact('komisi', 'summary'));
//     }

// }
