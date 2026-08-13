<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->subDays(6)->toDateString());
        $to   = $request->get('to', now()->toDateString());

        /* ===============================
         | KPI
         =============================== */
        $summary = DB::table('wa_logs')
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'SUCCESS') as success,
                SUM(status = 'FAILED') as failed
            ")
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->first();

        /* ===============================
         | BY TYPE
         =============================== */
        $byType = DB::table('wa_logs')
            ->selectRaw("type, COUNT(*) as total")
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->groupBy('type')
            ->pluck('total', 'type');

        /* ===============================
         | DAILY TREND
         =============================== */
        $daily = DB::table('wa_logs')
            ->selectRaw("
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(status='SUCCESS') as success,
                SUM(status='FAILED') as failed
            ")
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        /* ===============================
         | FAILED LIST (TOP)
         =============================== */
        $failedList = DB::table('wa_logs')
            ->where('status', 'FAILED')
            ->latest()
            ->limit(20)
            ->get();

        return view('keuangan.analytics.wa', compact(
            'from',
            'to',
            'summary',
            'byType',
            'daily',
            'failedList'
        ));
    }
}
