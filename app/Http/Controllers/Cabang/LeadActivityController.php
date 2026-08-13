<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\Lead\LeadActivityService;
use Illuminate\Http\Request;

class LeadActivityController extends Controller
{
    public function __construct(
        protected LeadActivityService $activityService
    ) {}

    public function store(Request $request, Lead $lead)
    {
        // 🔐 SCOPE CABANG (WAJIB)
        abort_unless(
            $lead->branch_id === auth()->user()->branch_id,
            403
        );

        // 🚫 LEAD CLOSED TIDAK BOLEH FOLLOW UP
        abort_if($lead->status === 'CLOSED', 403);

        $data = $request->validate([
            'aktivitas'   => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi',
            'hasil'       => 'required|string',
            'next_action' => 'nullable|string',
        ]);

        $this->activityService->log($lead, $data);

        return back()->with('success', 'Follow up berhasil disimpan.');
    }
}

// namespace App\Http\Controllers\Cabang;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Services\Lead\LeadActivityService;
// use Illuminate\Http\Request;

// class LeadActivityController extends Controller
// {
//     /**
//      * =====================================================
//      * SIMPAN FOLLOW UP (CABANG)
//      * =====================================================
//      * - Hanya untuk lead cabang sendiri
//      * - Tidak boleh sentuh CRM
//      * - Pipeline auto via LeadActivityService
//      */
//     public function store(Request $request, Lead $lead)
//     {
//         // 🔐 Ambil context (SINGLE SOURCE)
//         $ctx = app('access.context');

//         /**
//          * =====================================================
//          * AUTHORIZATION (LEVEL CABANG)
//          * =====================================================
//          */
//         abort_unless(
//             $ctx['branch_id']
//             && $lead->branch_id === $ctx['branch_id'],
//             403,
//             'Lead bukan milik cabang Anda.'
//         );

//         // 🚫 Lead sudah closed → tidak boleh follow up
//         abort_if(
//             $lead->status === 'CLOSED',
//             403,
//             'Lead sudah ditutup.'
//         );

//         /**
//          * =====================================================
//          * VALIDATION
//          * =====================================================
//          */
//         $data = $request->validate([
//             'aktivitas'   => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi',
//             'hasil'       => 'required|string|max:1000',
//             'next_action' => 'nullable|string|max:1000',
//         ]);

//         /**
//          * =====================================================
//          * SINGLE SOURCE OF TRUTH
//          * =====================================================
//          * - Insert lead_activities
//          * - Auto move pipeline
//          * - Pipeline log
//          */
//         app(LeadActivityService::class)->log($lead, $data);

//         return back()->with(
//             'success',
//             'Follow up berhasil ditambahkan.'
//         );
//     }
// }
