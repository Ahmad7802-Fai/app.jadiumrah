<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadClosing;
use App\Models\Pipeline;
use Illuminate\Support\Facades\DB;

class SalesDashboardController extends Controller
{
    public function index()
    {

        /* ======================================================
         | 1️⃣ SUMMARY COUNTERS
         ====================================================== */
        $summary = $this->summaryCounters();

        /* ======================================================
         | 2️⃣ PIPELINE CHART
         ====================================================== */
        $pipelineChart = $this->pipelineChart();

        /* ======================================================
         | 3️⃣ MONTHLY CLOSING
         ====================================================== */
        $monthlyClosing = $this->monthlyClosing();
        $recentClosings = LeadClosing::with(['lead'])
            ->where('status', 'APPROVED')
            ->whereNotNull('closed_at')
            ->orderByDesc('closed_at')
            ->limit(5)
            ->get();

        return view('crm.dashboard.sales', array_merge(
            $summary,
            compact('pipelineChart', 'monthlyClosing', 'recentClosings')
        ));

    }

    /* ======================================================
     | SUMMARY COUNTERS
     ====================================================== */
    private function summaryCounters(): array
    {
        return [
            'totalLeads' => Lead::count(),

            'closingCount' => LeadClosing::count(),

            'closingTotal' => (int) LeadClosing::sum('nominal_dp'),

            'followUpDueToday' => LeadActivity::whereDate(
                'followup_date',
                today()
            )->count(),

            'followUpOverdue' => LeadActivity::whereNotNull('followup_date')
                ->where('followup_date', '<', now())
                ->count(),
        ];
    }

    /* ======================================================
     | PIPELINE CHART
     ====================================================== */
    private function pipelineChart()
    {
        return Pipeline::withCount('leads')
            ->orderBy('urutan')
            ->get()
            ->map(fn ($p) => [
                'label' => ucfirst(str_replace('_', ' ', $p->tahap)),
                'total' => $p->leads_count,
            ]);
    }

    /* ======================================================
     | MONTHLY CLOSING
     ====================================================== */
    private function monthlyClosing()
    {
        return LeadClosing::select(
                DB::raw('MONTH(closed_at) as month'),
                DB::raw('SUM(nominal_dp) as total')
            )
            ->whereNotNull('closed_at')
            ->where('status', 'APPROVED')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

}
