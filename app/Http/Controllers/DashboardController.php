<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Dashboard\DashboardFactory;

class DashboardController extends Controller
{
    /* =====================================================
     | DASHBOARD UTAMA (SEMUA ROLE)
     ===================================================== */
    public function index(Request $request)
    {
        $user = Auth::user();

        [$month, $year] = $this->resolvePeriod($request);

        $dashboard = DashboardFactory::make($user);

        return view(
            $dashboard->view(),
            [
                'title'           => $dashboard->cards($month, $year)['title'],
                'cards'           => $dashboard->cards($month, $year),
                'chart'           => $dashboard->chart($month, $year),
                'chartComparison' => $dashboard->chartComparison(),
                'month'           => $month,
                'year'            => $year,
            ]
        );
    }

    /* =====================================================
     | AJAX — CHART TOTAL
     ===================================================== */
    public function chartData(Request $request)
    {
        $user = Auth::user();

        [$month, $year] = $this->resolvePeriod($request);

        $dashboard = DashboardFactory::make($user);

        return response()->json(
            $dashboard->chart($month, $year)
        );
    }

    /* =====================================================
     | AJAX — CHART COMPARISON
     ===================================================== */
    public function chartComparison()
    {
        $user = Auth::user();

        $dashboard = DashboardFactory::make($user);

        return response()->json(
            $dashboard->chartComparison()
        );
    }

    /* =====================================================
     | INTERNAL — PERIOD PARSER (YYYY-MM)
     ===================================================== */
    private function resolvePeriod(Request $request): array
    {
        /**
         * Expected:
         * ?month=2026-01
         */
        $period = $request->get('month');

        if (
            $period &&
            preg_match('/^\d{4}-\d{2}$/', $period)
        ) {
            [$year, $month] = explode('-', $period);

            $month = max(1, min(12, (int) $month));
            $year  = (int) $year;

            return [$month, $year];
        }

        // fallback aman
        return [now()->month, now()->year];
    }
}

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Services\Dashboard\DashboardFactory;

// class DashboardController extends Controller
// {
//     /* =====================================================
//      | DASHBOARD UTAMA (SEMUA ROLE)
//      ===================================================== */
// public function index(Request $request)
// {
//     $user = Auth::user();

//     $month = (int) $request->get('month', now()->month);
//     $year  = (int) $request->get('year', now()->year);

//     $dashboard = DashboardFactory::make($user);

//     return view(
//         $dashboard->view(),
//         [
//             'title'            => $dashboard->cards($month, $year)['title'],
//             'cards'            => $dashboard->cards($month, $year),
//             'chart'            => $dashboard->chart($month, $year),
//             'chartComparison'  => $dashboard->chartComparison(),
//             'month'            => $month,
//             'year'             => $year,
//         ]
//     );
// }

//     /* =====================================================
//      | AJAX — CHART TOTAL
//      ===================================================== */
//     public function chartData(Request $request)
//     {
//         $user = Auth::user();

//         $month = (int) $request->get('month', now()->month);
//         $year  = (int) $request->get('year', now()->year);

//         $dashboard = DashboardFactory::make($user);

//         return response()->json(
//             $dashboard->chart($month, $year)
//         );
//     }

//     /* =====================================================
//      | AJAX — CHART COMPARISON
//      ===================================================== */
//     public function chartComparison()
//     {
//         $user = Auth::user();

//         $dashboard = DashboardFactory::make($user);

//         return response()->json(
//             $dashboard->chartComparison()
//         );
//     }
// }

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Services\Dashboard\DashboardFactory;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         $user = Auth::user();

//         // ===============================
//         // FILTER BULAN & TAHUN
//         // ===============================
//         $month = (int) $request->get('month', now()->month);
//         $year  = (int) $request->get('year', now()->year);

//         // ===============================
//         // RESOLVE DASHBOARD BY ROLE
//         // ===============================
//         $dashboard = DashboardFactory::make(
//             strtolower($user->role ?? 'guest')
//         );

//         $data = $dashboard->cards($month, $year);

//         return view(
//             $dashboard->view(),
//             array_merge($data, compact('month', 'year'))
//         );
//     }

//     /**
//      * ===============================
//      * CHART TOTAL
//      * ===============================
//      */
//     public function chartData(Request $request)
//     {
//         $month = (int) $request->get('month', now()->month);
//         $year  = (int) $request->get('year', now()->year);

//         $dashboard = DashboardFactory::make(
//             strtolower(Auth::user()->role ?? 'guest')
//         );

//         return response()->json(
//             $dashboard->chart($month, $year)
//         );
//     }

//     /**
//      * ===============================
//      * CHART COMPARISON
//      * ===============================
//      */
//     public function chartComparison()
//     {
//         $dashboard = DashboardFactory::make(
//             strtolower(Auth::user()->role ?? 'guest')
//         );

//         return response()->json(
//             $dashboard->chartComparison()
//         );
//     }
// }

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Services\Dashboard\DashboardFactory;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         $user = Auth::user();

//         /**
//          * ==========================================
//          * REDIRECT KHUSUS
//          * ==========================================
//          */
//         if ($user->role === 'SALES' && $user->agent_id) {
//             return redirect()->route('agent.dashboard');
//         }

//         if ($user->role === 'ADMIN' && $user->branch_id) {
//             return redirect()->route('cabang.dashboard');
//         }

//         /**
//          * ==========================================
//          * FILTER BULAN & TAHUN
//          * ==========================================
//          */
//         $month = (int) $request->get('month', now()->month);
//         $year  = (int) $request->get('year', now()->year);

//         /**
//          * ==========================================
//          * DASHBOARD RESOLVE
//          * ==========================================
//          */
//         $dashboard = DashboardFactory::make(
//             strtolower($user->role ?? 'guest')
//         );

//         $data = $dashboard->cards($month, $year);

//         return view(
//             $dashboard->view(), // 🔥 AUTO VIEW
//             array_merge($data, compact('month', 'year'))
//         );
//     }

//     /**
//      * ===============================
//      * CHART TOTAL / PIE
//      * ===============================
//      */
//     public function chartData(Request $request)
//     {
//         $month = (int) $request->get('month', now()->month);
//         $year  = (int) $request->get('year', now()->year);

//         $dashboard = DashboardFactory::make(
//             strtolower(Auth::user()->role ?? 'guest')
//         );

//         return response()->json(
//             $dashboard->chart($month, $year)
//         );
//     }

//     /**
//      * ===============================
//      * CHART COMPARISON
//      * ===============================
//      */
//     public function chartComparison()
//     {
//         $dashboard = DashboardFactory::make(
//             strtolower(Auth::user()->role ?? 'guest')
//         );

//         return response()->json(
//             $dashboard->chartComparison()
//         );
//     }
// }
