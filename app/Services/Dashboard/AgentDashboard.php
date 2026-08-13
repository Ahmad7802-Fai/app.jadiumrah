<?php

namespace App\Services\Dashboard;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Jamaah;
use Illuminate\Support\Carbon;
use App\Models\KomisiLogs;
use Illuminate\Support\Facades\DB;

class AgentDashboard
{
    protected int $agentId;
    protected Agent $agent;

    public function __construct(int $agentId)
    {
        $this->agentId = $agentId;
        $this->agent   = Agent::findOrFail($agentId);
    }

    /**
     * PUBLIC API
     */
    public function kpi(): array
    {
        return array_merge(
            $this->leadStats(),
            $this->followUpStats(),
            $this->jamaahStats(),
            $this->komisiStats(),
        );
    }

    /* ==================================================
       LEAD
    ================================================== */
    private function leadStats(): array
    {
        $total = Lead::where('agent_id', $this->agentId)->count();

        $active = Lead::where('agent_id', $this->agentId)
            ->whereIn('status', ['NEW', 'ACTIVE'])
            ->count();

        $closed = Lead::where('agent_id', $this->agentId)
            ->where('status', 'CLOSED')
            ->count();

        return [
            'total_lead'      => $total,
            'active_lead'     => $active,
            'closing_lead'    => $closed,
            'conversion_rate' => $total > 0
                ? round(($closed / $total) * 100, 1)
                : 0,
        ];
    }

    /* ==================================================
       FOLLOW UP
    ================================================== */
    private function followUpStats(): array
    {
        $today = Carbon::today();

        return [
            'total_followup' => LeadActivity::where('user_id', $this->agent->user_id)->count(),

            'followup_today' => LeadActivity::where('user_id', $this->agent->user_id)
                ->whereDate('created_at', $today)
                ->count(),

            'followup_upcoming' => LeadActivity::where('user_id', $this->agent->user_id)
                ->whereNotNull('followup_date')
                ->whereDate('followup_date', '>=', $today)
                ->count(),
        ];
    }

    /* ==================================================
       JAMAAH
    ================================================== */
    private function jamaahStats(): array
    {
        $base = Jamaah::where('agent_id', $this->agentId);

        return [
            'total_jamaah' => (clone $base)->count(),

            'jamaah_approved' => (clone $base)
                ->where('status', 'approved')
                ->count(),

            'jamaah_lunas' => (clone $base)
                ->where('sisa', 0)
                ->count(),

            'jamaah_siap_komisi' => (clone $base)
                ->where('status', 'approved')
                ->where('sisa', 0)
                ->count(),
        ];
    }

    /* ==================================================
       KOMISI
    ================================================== */
    private function komisiStats(): array
    {
        $base = KomisiLogs::where('agent_id', $this->agentId);

        $totalKomisi = (clone $base)->sum('nominal');

        $pending = (clone $base)
            ->where('status', 'pending')
            ->sum('nominal');

        $siapDicairkan = (clone $base)
            ->where('status', 'ready')
            ->sum('nominal');

        $sudahDibayar = (clone $base)
            ->where('status', 'paid')
            ->sum('nominal');

        return [
            'total_komisi'        => (int) $totalKomisi,
            'komisi_pending'     => (int) $pending,
            'komisi_siap_cair'   => (int) $siapDicairkan,
            'komisi_dibayar'     => (int) $sudahDibayar,
        ];
    }

}

// namespace App\Services\Dashboard;

// use App\Models\Jamaah;
// use App\Models\Payments;

// class AgentDashboard
// {
//     protected int $agentId;

//     public function __construct(int $agentId)
//     {
//         $this->agentId = $agentId;
//     }

//     /**
//      * KPI CARDS
//      */
//     public function cards(): array
//     {
//         $base = Jamaah::where('agent_id', $this->agentId);

//         return [
//             [
//                 'key'   => 'total_jamaah',
//                 'label' => 'Total Jamaah',
//                 'value' => (clone $base)->count(),
//             ],
//             [
//                 'key'   => 'jamaah_manual',
//                 'label' => 'Jamaah Manual',
//                 'value' => (clone $base)
//                     ->where('mode', 'manual')
//                     ->count(),
//             ],
//             [
//                 'key'   => 'jamaah_affiliate',
//                 'label' => 'Jamaah Affiliate',
//                 'value' => (clone $base)
//                     ->where('mode', 'affiliate')
//                     ->count(),
//             ],
//             [
//                 'key'   => 'total_pembayaran',
//                 'label' => 'Total Pembayaran Valid',
//                 'value' => Payments::whereIn(
//                         'jamaah_id',
//                         (clone $base)->select('id')
//                     )
//                     ->where('status', Payments::STATUS_VALID)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     /**
//      * JAMAAH TERBARU
//      */
//     public function recentJamaah(int $limit = 5)
//     {
//         return Jamaah::where('agent_id', $this->agentId)
//             ->latest()
//             ->limit($limit)
//             ->get();
//     }

//     public function kpi(): array
//     {
//         // LEAD (sementara pakai jamaah manual sebagai lead)
//         $totalLead = Jamaah::where('agent_id', $this->agentId)
//             ->where('mode', 'manual')
//             ->count();

//         $activeLead = Jamaah::where('agent_id', $this->agentId)
//             ->where('mode', 'manual')
//             ->where('status', 'draft')
//             ->count();

//         $closingLead = Jamaah::where('agent_id', $this->agentId)
//             ->where('mode', 'manual')
//             ->where('status', 'approved')
//             ->count();

//         $conversionRate = $totalLead > 0
//             ? round(($closingLead / $totalLead) * 100, 1)
//             : 0;

//         // FOLLOW UP (placeholder, karena tabel followup belum kita sentuh)
//         $totalFollowup      = 0;
//         $followupToday      = 0;
//         $followupUpcoming   = 0;

//         // JAMAAH
//         $totalJamaah = Jamaah::where('agent_id', $this->agentId)->count();

//         $jamaahAktif = Jamaah::where('agent_id', $this->agentId)
//             ->where('sisa', '>', 0)
//             ->count();

//         $jamaahLunas = Jamaah::where('agent_id', $this->agentId)
//             ->where('sisa', '<=', 0)
//             ->count();

//         return [
//             // LEAD
//             'total_lead'        => $totalLead,
//             'active_lead'       => $activeLead,
//             'closing_lead'      => $closingLead,
//             'conversion_rate'   => $conversionRate,

//             // FOLLOW UP
//             'total_followup'    => $totalFollowup,
//             'followup_today'    => $followupToday,
//             'followup_upcoming' => $followupUpcoming,

//             // JAMAAH
//             'total_jamaah'      => $totalJamaah,
//             'jamaah_aktif'      => $jamaahAktif,
//             'jamaah_lunas'      => $jamaahLunas,
//         ];
//     }

// }


// namespace App\Services\Dashboard;

// use App\Models\Jamaah;
// use App\Models\Payments;

// class AgentDashboard
// {
//     protected int $agentId;

//     public function __construct(int $agentId)
//     {
//         $this->agentId = $agentId;
//     }

//     /**
//      * CARD SUMMARY
//      */
//     public function cards(): array
//     {
//         $baseQuery = Jamaah::where('agent_id', $this->agentId);

//         return [
//             [
//                 'label' => 'Total Jamaah',
//                 'value' => (clone $baseQuery)->count(),
//             ],
//             [
//                 'label' => 'Jamaah Aktif',
//                 'value' => (clone $baseQuery)
//                     ->where('status', 'approved')
//                     ->where('sisa', '>', 0)
//                     ->count(),
//             ],
//             [
//                 'label' => 'Jamaah Lunas',
//                 'value' => (clone $baseQuery)
//                     ->where('status', 'approved')
//                     ->where('sisa', '<=', 0)
//                     ->count(),
//             ],
//             [
//                 'label' => 'Total Pembayaran',
//                 'value' => Payments::whereIn(
//                         'jamaah_id',
//                         (clone $baseQuery)->select('id')
//                     )
//                     ->where('status', Payments::STATUS_VALID)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     /**
//      * JAMAAH TERBARU
//      */
//     public function recentJamaah(int $limit = 5)
//     {
//         return Jamaah::where('agent_id', $this->agentId)
//             ->latest()
//             ->limit($limit)
//             ->get();
//     }
// }


// namespace App\Services\Dashboard;

// use App\Models\Jamaah;
// use App\Models\Payments;

// class AgentDashboard
// {
//     protected int $agentId;

//     public function __construct(int $agentId)
//     {
//         $this->agentId = $agentId;
//     }

//     public function cards(): array
//     {
//         $jamaahQuery = Jamaah::where('agent_id', $this->agentId);

//         $jamaahIds = $jamaahQuery->pluck('id');

//         return [
//             [
//                 'label' => 'Total Jamaah',
//                 'value' => $jamaahIds->count(),
//             ],
//             [
//                 'label' => 'Jamaah Aktif',
//                 'value' => Jamaah::whereIn('id', $jamaahIds)
//                     ->where('sisa', '>', 0)
//                     ->count(),
//             ],
//             [
//                 'label' => 'Jamaah Lunas',
//                 'value' => Jamaah::whereIn('id', $jamaahIds)
//                     ->where('sisa', '<=', 0)
//                     ->count(),
//             ],
//             [
//                 'label' => 'Total Pembayaran',
//                 'value' => Payments::whereIn('jamaah_id', $jamaahIds)
//                     ->sum('jumlah'),
//             ],
//         ];
//     }

//     public function recentJamaah()
//     {
//         return Jamaah::where('agent_id', $this->agentId)
//             ->latest()
//             ->limit(5)
//             ->get();
//     }
// }
