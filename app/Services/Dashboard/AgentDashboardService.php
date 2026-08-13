<?php

namespace App\Services\Dashboard;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Jamaah;
use App\Services\Komisi\AgentKomisiDashboardService;
use Illuminate\Support\Carbon;

class AgentDashboardService
{
    protected int $agentId;
    protected Agent $agent;

    public function __construct(
        protected AgentKomisiDashboardService $komisiService
    ) {}

    /**
     * SET CONTEXT (WAJIB)
     */
    public function forAgent(int $agentId): self
    {
        $this->agentId = $agentId;
        $this->agent   = Agent::findOrFail($agentId);

        return $this;
    }

    /**
     * KPI DASHBOARD
     */
    public function kpi(): array
    {
        return [
            ...$this->leadStats(),
            ...$this->followUpStats(),
            ...$this->jamaahStats(),

            // 🔑 KOMISI (SUMBER RESMI)
            'komisi' => $this->komisiService
                ->summary($this->agentId),
        ];
    }

    /* ======================
       LEAD
    ====================== */
    private function leadStats(): array
    {
        $total = Lead::where('agent_id', $this->agentId)->count();
        $closed = Lead::where('agent_id', $this->agentId)
            ->where('status', 'CLOSED')
            ->count();

        return [
            'total_lead' => $total,
            'active_lead' => Lead::where('agent_id', $this->agentId)
                ->whereIn('status', ['NEW', 'ACTIVE'])
                ->count(),
            'closing_lead' => $closed,
            'conversion_rate' => $total > 0
                ? round(($closed / $total) * 100, 1)
                : 0,
        ];
    }

    /* ======================
       FOLLOW UP
    ====================== */
    private function followUpStats(): array
    {
        $today = Carbon::today();

        return [
            'total_followup' => LeadActivity::where('user_id', $this->agent->user_id)->count(),
            'followup_today' => LeadActivity::where('user_id', $this->agent->user_id)
                ->whereDate('created_at', $today)
                ->count(),
            'followup_upcoming' => LeadActivity::where('user_id', $this->agent->user_id)
                ->whereDate('followup_date', '>=', $today)
                ->count(),
        ];
    }

    /* ======================
       JAMAAH
    ====================== */
private function jamaahStats(): array
{
    $jamaah = Jamaah::with('payments')
        ->where('agent_id', $this->agentId)
        ->get();

    $jamaahLunas = $jamaah->filter(function ($j) {

        // harga referensi
        $harga = $j->harga_disepakati ?? $j->harga_default;

        // kalau belum ada harga → tidak bisa lunas
        if (!$harga || $harga <= 0) {
            return false;
        }

        // total pembayaran VALID
        $totalBayar = $j->payments
            ->where('status', 'valid')
            ->sum('jumlah');

        return $totalBayar >= $harga;
    });

    return [
        'total_jamaah' => $jamaah->count(),

        'jamaah_approved' => $jamaah
            ->where('status', 'approved')
            ->count(),

        // 🔑 INI SEKARANG AKAN SINKRON
        'jamaah_lunas' => $jamaahLunas->count(),

        'jamaah_siap_komisi' => $jamaahLunas
            ->where('status', 'approved')
            ->count(),
    ];
}


}

// namespace App\Services\Dashboard;

// use App\Models\Agent;
// use App\Models\Lead;
// use App\Models\LeadActivity;
// use App\Models\Jamaah;
// use App\Services\Komisi\AgentKomisiDashboardService;
// use Illuminate\Support\Carbon;

// class AgentDashboardService
// {
//     protected Agent $agent;

//     public function __construct(
//         protected int $agentId,
//         protected AgentKomisiDashboardService $komisiService
//     ) {
//         $this->agent = Agent::findOrFail($agentId);
//     }

//     public function kpi(): array
//     {
//         return [
//             ...$this->leadStats(),
//             ...$this->followUpStats(),
//             ...$this->jamaahStats(),

//             // 🔑 KPI KOMISI (SUMBER RESMI)
//             'komisi' => $this->komisiKpi(),
//         ];
//     }

//     /* ======================
//        KOMISI KPI (SUMMARY)
//     ====================== */
//     private function komisiKpi(): array
//     {
//         $summary = $this->komisiService
//             ->summary($this->agentId);

//         return [
//             'total'     => $summary['total'],
//             'pending'   => $summary['pending'],
//             'available' => $summary['available'],
//             'paid'      => $summary['paid'],
//         ];
//     }

//     /* ======================
//        LEAD
//     ====================== */
//     private function leadStats(): array
//     {
//         $total = Lead::where('agent_id', $this->agentId)->count();
//         $closed = Lead::where('agent_id', $this->agentId)
//             ->where('status', 'CLOSED')
//             ->count();

//         return [
//             'total_lead' => $total,
//             'active_lead' => Lead::where('agent_id', $this->agentId)
//                 ->whereIn('status', ['NEW', 'ACTIVE'])
//                 ->count(),
//             'closing_lead' => $closed,
//             'conversion_rate' => $total > 0
//                 ? round(($closed / $total) * 100, 1)
//                 : 0,
//         ];
//     }

//     /* ======================
//        FOLLOW UP
//     ====================== */
//     private function followUpStats(): array
//     {
//         $today = Carbon::today();

//         return [
//             'total_followup' => LeadActivity::where('user_id', $this->agent->user_id)->count(),
//             'followup_today' => LeadActivity::where('user_id', $this->agent->user_id)
//                 ->whereDate('created_at', $today)
//                 ->count(),
//             'followup_upcoming' => LeadActivity::where('user_id', $this->agent->user_id)
//                 ->whereDate('followup_date', '>=', $today)
//                 ->count(),
//         ];
//     }

//     /* ======================
//        JAMAAH
//     ====================== */
//     private function jamaahStats(): array
//     {
//         $base = Jamaah::where('agent_id', $this->agentId);

//         return [
//             'total_jamaah' => (clone $base)->count(),
//             'jamaah_approved' => (clone $base)->where('status', 'approved')->count(),
//             'jamaah_lunas' => (clone $base)->where('sisa', 0)->count(),
//             'jamaah_siap_komisi' => (clone $base)
//                 ->where('status', 'approved')
//                 ->where('sisa', 0)
//                 ->count(),
//         ];
//     }
// }


// namespace App\Services\Dashboard;

// use App\Models\Agent;
// use App\Models\Lead;
// use App\Models\LeadActivity;
// use App\Models\Jamaah;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;

// class AgentDashboardService
// {
//     public function getKpi(int $userId): array
//     {
//         $today = Carbon::today();

//         /* ======================
//            AGENT
//         ====================== */
//         $agent = Agent::where('user_id', $userId)->first();

//         if (!$agent) {
//             return $this->emptyKpi();
//         }

//         $agentId = $agent->id;
//         $komisiPersen = $agent->komisi_persen ?? 0;

//         /* ======================
//            LEAD
//         ====================== */
//         $totalLead = Lead::where('agent_id', $agentId)->count();

//         $activeLead = Lead::where('agent_id', $agentId)
//             ->whereIn('status', ['NEW', 'ACTIVE'])
//             ->count();

//         $closedLead = Lead::where('agent_id', $agentId)
//             ->where('status', 'CLOSED')
//             ->count();

//         $conversionRate = $totalLead > 0
//             ? round(($closedLead / $totalLead) * 100, 1)
//             : 0;

//         /* ======================
//            FOLLOW UP
//         ====================== */
//         $totalFollowUp = LeadActivity::where('user_id', $userId)->count();

//         $followUpToday = LeadActivity::where('user_id', $userId)
//             ->whereDate('created_at', $today)
//             ->count();

//         $followUpUpcoming = LeadActivity::where('user_id', $userId)
//             ->whereNotNull('followup_date')
//             ->whereDate('followup_date', '>=', $today)
//             ->count();

//         /* ======================
//            JAMAAH
//         ====================== */
//         $jamaahBase = Jamaah::where('agent_id', $agentId);

//         $totalJamaah = (clone $jamaahBase)->count();

//         // jamaah yang sudah di-approve admin
//         $jamaahApproved = (clone $jamaahBase)
//             ->where('status', 'approved')
//             ->count();

//         // jamaah yang sudah lunas pembayaran (tanpa peduli approval)
//         $jamaahLunas = (clone $jamaahBase)
//             ->where('sisa', 0)
//             ->count();

//         // jamaah yang valid untuk komisi
//         $jamaahSiapKomisi = (clone $jamaahBase)
//             ->where('status', 'approved')
//             ->where('sisa', 0)
//             ->count();

//         /* ======================
//            KOMISI
//         ====================== */
//         $totalKomisi = Jamaah::where('agent_id', $agentId)
//             ->where('status', 'approved')
//             ->where('sisa', 0)
//             ->whereNotNull('harga')
//             ->sum(DB::raw("harga * {$komisiPersen} / 100"));

//         /* ======================
//            RETURN KPI
//         ====================== */
//         return [
//             // LEAD
//             'total_lead'        => $totalLead,
//             'active_lead'       => $activeLead,
//             'closing_lead'      => $closedLead,
//             'conversion_rate'   => $conversionRate,

//             // FOLLOW UP
//             'total_followup'    => $totalFollowUp,
//             'followup_today'    => $followUpToday,
//             'followup_upcoming' => $followUpUpcoming,

//             // JAMAAH
//             'total_jamaah'       => $totalJamaah,
//             'jamaah_approved'    => $jamaahApproved,
//             'jamaah_lunas'       => $jamaahLunas,
//             'jamaah_siap_komisi' => $jamaahSiapKomisi,

//             // KOMISI
//             'komisi_persen' => $komisiPersen,
//             'total_komisi'  => (int) $totalKomisi,
//         ];
//     }

//     private function emptyKpi(): array
//     {
//         return [
//             'total_lead' => 0,
//             'active_lead' => 0,
//             'closing_lead' => 0,
//             'conversion_rate' => 0,

//             'total_followup' => 0,
//             'followup_today' => 0,
//             'followup_upcoming' => 0,

//             'total_jamaah' => 0,
//             'jamaah_approved' => 0,
//             'jamaah_lunas' => 0,
//             'jamaah_siap_komisi' => 0,

//             'komisi_persen' => 0,
//             'total_komisi' => 0,
//         ];
//     }
// }


// namespace App\Services\Dashboard;

// use App\Models\Lead;
// use App\Models\LeadActivity;
// use App\Models\Jamaah;
// use Illuminate\Support\Carbon;
// use App\Models\Agent;
// class AgentDashboardService
// {
//     public function getKpi(int $userId): array
//     {
//         $today = Carbon::today();

//         // 🔑 Ambil agent berdasarkan user login
//         $agent = Agent::where('user_id', $userId)->first();

//         // kalau user bukan agent
//         if (!$agent) {
//             return $this->emptyKpi();
//         }

//         $agentId = $agent->id;

//         /* ======================
//            LEAD
//         ====================== */
//         $totalLead = Lead::where('agent_id', $agentId)->count();

//         $activeLead = Lead::where('agent_id', $agentId)
//             ->whereIn('status', ['NEW', 'ACTIVE'])
//             ->count();

//         $closedLead = Lead::where('agent_id', $agentId)
//             ->where('status', 'CLOSED')
//             ->count();

//         /* ======================
//            FOLLOW UP
//         ====================== */
//         $totalFollowUp = LeadActivity::where('user_id', $userId)->count();

//         $followUpToday = LeadActivity::where('user_id', $userId)
//             ->whereDate('created_at', $today)
//             ->count();

//         $followUpUpcoming = LeadActivity::where('user_id', $userId)
//             ->whereNotNull('followup_date')
//             ->whereDate('followup_date', '>=', $today)
//             ->count();

//         /* ======================
//            JAMAAH
//         ====================== */
//         $jamaahQuery = Jamaah::where('agent_id', $agentId);

//         $totalJamaah = (clone $jamaahQuery)->count();

//         $jamaahAktif = (clone $jamaahQuery)
//             ->where('status', 'approved')
//             ->count();

//         $jamaahLunas = (clone $jamaahQuery)
//             ->where('status', 'approved')
//             ->where('sisa', 0)
//             ->count();

//         return [
//             'total_lead'        => $totalLead,
//             'active_lead'       => $activeLead,
//             'closing_lead'      => $closedLead,
//             'conversion_rate'   => $totalLead > 0
//                 ? round(($closedLead / $totalLead) * 100, 1)
//                 : 0,

//             'total_followup'    => $totalFollowUp,
//             'followup_today'    => $followUpToday,
//             'followup_upcoming' => $followUpUpcoming,

//             'total_jamaah'      => $totalJamaah,
//             'jamaah_aktif'      => $jamaahAktif,
//             'jamaah_lunas'      => $jamaahLunas,
//         ];
//     }

//     private function emptyKpi(): array
//     {
//         return [
//             'total_lead' => 0,
//             'active_lead' => 0,
//             'closing_lead' => 0,
//             'conversion_rate' => 0,
//             'total_followup' => 0,
//             'followup_today' => 0,
//             'followup_upcoming' => 0,
//             'total_jamaah' => 0,
//             'jamaah_aktif' => 0,
//             'jamaah_lunas' => 0,
//         ];
//     }
// }
