<?php

namespace App\Services\Marketing;

use App\Models\Lead;
use App\Models\MarketingExpenses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketingExpenseService
{
    /* =====================================================
     | LIST
    ===================================================== */
    public function list(array $filters = [])
    {
        return MarketingExpenses::query()
            ->with('sumber')
            ->when($filters['platform'] ?? null, fn ($q, $v) =>
                $q->where('platform', $v)
            )
            ->when($filters['bulan'] ?? null, fn ($q, $v) =>
                $q->whereMonth('tanggal', $v)
            )
            ->orderByDesc('tanggal')
            ->paginate(15);
    }

    /* =====================================================
     | CREATE
    ===================================================== */
    public function create(array $data): MarketingExpenses
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            return MarketingExpenses::create($data);
        });
    }

    /* =====================================================
     | UPDATE
    ===================================================== */
    public function update(MarketingExpenses $expense, array $data): MarketingExpenses
    {
        $expense->update($data);
        return $expense;
    }

    /* =====================================================
     | DELETE
    ===================================================== */
    public function delete(MarketingExpenses $expense): void
    {
        $expense->delete();
    }

    /* =====================================================
     | SUMMARY
    ===================================================== */
    public function summary(array $filters = []): array
    {
        $q = MarketingExpenses::query();

        if (!empty($filters['bulan'])) {
            $q->whereMonth('tanggal', $filters['bulan']);
        }

        if (!empty($filters['platform'])) {
            $q->where('platform', $filters['platform']);
        }

        return [
            'total_biaya' => (int) (clone $q)->sum('biaya'),

            'total_campaign' => (int) (clone $q)
                ->whereNotNull('nama_campaign')
                ->distinct('nama_campaign')
                ->count(),

            'top_platform' => (clone $q)
                ->selectRaw('platform, SUM(biaya) as total')
                ->groupBy('platform')
                ->orderByDesc('total')
                ->value('platform'),
        ];
    }

    /* =====================================================
     | COST PER LEAD (CPL)
    ===================================================== */
    public function costPerLead(array $filters = []): array
    {
        $expenses = MarketingExpenses::query()
            ->select('sumber_id', DB::raw('SUM(biaya) as total_biaya'))
            ->groupBy('sumber_id')
            ->with('sumber')
            ->get();

        return $expenses->map(function ($expense) use ($filters) {

            $leadQuery = Lead::query()
                ->where('sumber_id', $expense->sumber_id);

            if (!empty($filters['bulan'])) {
                $leadQuery->whereMonth('created_at', $filters['bulan']);
            }

            $totalLead = $leadQuery->count();

            return [
                'sumber'      => $expense->sumber->nama_sumber ?? '-',
                'total_biaya' => (int) $expense->total_biaya,
                'total_lead'  => $totalLead,
                'cpl'         => $totalLead > 0
                    ? (int) ($expense->total_biaya / $totalLead)
                    : 0,
            ];
        })->toArray();
    }

    /* =====================================================
     | ROI MARKETING (🔥 MAIN VALUE)
    ===================================================== */
    public function roiMarketing(array $filters = []): array
{
    /**
     * ===============================
     * TOTAL EXPENSE PER SUMBER
     * ===============================
     */
    $expenseQuery = MarketingExpenses::query()
        ->select('sumber_id', DB::raw('SUM(biaya) as total_biaya'))
        ->groupBy('sumber_id');

    if (!empty($filters['bulan'])) {
        $expenseQuery->whereMonth('tanggal', $filters['bulan']);
    }

    if (!empty($filters['platform'])) {
        $expenseQuery->where('platform', $filters['platform']);
    }

    $expenses = $expenseQuery
        ->with('sumber')
        ->get();

    $totalExpenseAll = (int) $expenses->sum('total_biaya');

    /**
     * ===============================
     * TOTAL REVENUE (INVOICE)
     * ===============================
     */
    $revenueQuery = DB::table('invoices');

    if (!empty($filters['bulan'])) {
        $revenueQuery->whereMonth('tanggal', $filters['bulan']);
    }

    $totalRevenueAll = (int) $revenueQuery->sum('total_tagihan');

    /**
     * ===============================
     * BUILD ROI DATA
     * ===============================
     */
    return $expenses->map(function ($expense) use ($totalExpenseAll, $totalRevenueAll) {

        $biaya = (int) $expense->total_biaya;

        // Revenue dibagi proporsional
        $revenue = $totalExpenseAll > 0
            ? (int) round(($biaya / $totalExpenseAll) * $totalRevenueAll)
            : 0;

        $profit = $revenue - $biaya;

        $roi = $biaya > 0
            ? round(($profit / $biaya) * 100, 2)
            : 0;

        return [
            'sumber'        => $expense->sumber->nama_sumber ?? '-',
            'total_biaya'   => $biaya,
            'total_revenue' => $revenue,
            'profit'        => $profit,
            'roi'           => $roi,
        ];
    })->toArray();
}

}

// namespace App\Services\Marketing;

// use App\Models\Lead;
// use App\Models\LeadSources;
// use App\Models\MarketingExpenses;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;

// class MarketingExpenseService
// {
//     /**
//      * List expenses with filter
//      */
//     public function list(array $filters = [])
//     {
//         return MarketingExpenses::query()
//             ->when($filters['platform'] ?? null, function ($q, $platform) {
//                 $q->where('platform', $platform);
//             })
//             ->when($filters['bulan'] ?? null, function ($q, $bulan) {
//                 $q->whereMonth('tanggal', $bulan);
//             })
//             ->orderByDesc('tanggal')
//             ->paginate(15);
//     }

//     /**
//      * Create expense
//      */
//     public function create(array $data): MarketingExpenses
//     {
//         return DB::transaction(function () use ($data) {

//             $data['created_by'] = Auth::id();

//             return MarketingExpenses::create($data);
//         });
//     }

//     /**
//      * Update expense
//      */
//     public function update(MarketingExpenses $expense, array $data): MarketingExpenses
//     {
//         return DB::transaction(function () use ($expense, $data) {

//             $expense->update($data);

//             return $expense;
//         });
//     }

//     /**
//      * Delete expense
//      */
//     public function delete(MarketingExpenses $expense): void
//     {
//         $expense->delete();
//     }

//     /**
//      * Summary (total biaya)
//      */
//     public function summary(array $filters = []): array
//     {
//         $baseQuery = MarketingExpenses::query();

//         // FILTER BULAN
//         if (!empty($filters['bulan'])) {
//             $baseQuery->whereMonth('tanggal', $filters['bulan']);
//         }

//         // FILTER PLATFORM
//         if (!empty($filters['platform'])) {
//             $baseQuery->where('platform', $filters['platform']);
//         }

//         // TOTAL BIAYA
//         $totalBiaya = (int) (clone $baseQuery)->sum('biaya');

//         // TOTAL CAMPAIGN (unik, tidak NULL)
//         $totalCampaign = (int) (clone $baseQuery)
//             ->whereNotNull('nama_campaign')
//             ->distinct('nama_campaign')
//             ->count('nama_campaign');

//         // PLATFORM TERBESAR (berdasarkan SUM biaya)
//         $platformTerbesar = (clone $baseQuery)
//             ->selectRaw('platform, SUM(biaya) as total')
//             ->whereNotNull('platform')
//             ->groupBy('platform')
//             ->orderByDesc('total')
//             ->value('platform');

//         return [
//             'total_biaya'        => $totalBiaya,
//             'total_campaign'     => $totalCampaign,
//             'platform_terbesar'  => $platformTerbesar,
//         ];
//     }

// public function costPerLead(array $filters = []): array
// {
//     $query = MarketingExpenses::query()
//         ->select('sumber_id', DB::raw('SUM(biaya) as total_biaya'))
//         ->groupBy('sumber_id');

//     if (!empty($filters['bulan'])) {
//         $query->whereMonth('tanggal', $filters['bulan']);
//     }

//     if (!empty($filters['platform'])) {
//         $query->where('platform', $filters['platform']);
//     }

//     $expenses = $query->with('sumber')->get();

//     return $expenses->map(function ($expense) use ($filters) {

//         $leadQuery = Lead::query()
//             ->where('sumber_id', $expense->sumber_id);

//         if (!empty($filters['bulan'])) {
//             $leadQuery->whereMonth('created_at', $filters['bulan']);
//         }

//         $totalLead = $leadQuery->count();

//         return [
//             'sumber'      => $expense->sumber->nama_sumber ?? '-',
//             'total_biaya' => (int) $expense->total_biaya,
//             'total_lead'  => $totalLead,
//             'cpl'         => $totalLead > 0
//                 ? (int) ($expense->total_biaya / $totalLead)
//                 : 0,
//         ];
//     })->toArray();
// }

// }
