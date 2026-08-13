<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\Lead\LeadPipelineService;
use App\Services\Lead\LeadActivityService;

class LeadActivityController extends Controller
{
       public function __construct(
        protected LeadActivityService $activityService
    ) {} 
    /* =====================================================
     | LIST FOLLOW UP (AGENDA)
     ===================================================== */
    public function index()
    {
        $list = LeadActivity::with(['lead', 'user'])
            ->whereNotNull('followup_date')
            ->orderBy('followup_date', 'asc')
            ->paginate(10);

        return view('crm.followup.index', compact('list'));
    }

    /* =====================================================
     | SIMPAN FOLLOW UP DARI DETAIL LEAD
     ===================================================== */
         public function store(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'aktivitas'   => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi',
            'hasil'       => 'required|string',
            'next_action' => 'nullable|string',
        ]);

        $this->activityService->log($lead, $data);

        return back()->with('success', 'Follow up berhasil ditambahkan.');
    }
    // public function store(Request $request, int $leadId)
    // {
    //     $lead = Lead::findOrFail($leadId);

    //     $data = $request->validate([
    //         'aktivitas'     => 'required|in:wa,telpon,kunjungan,presentasi,dm,meeting',
    //         'hasil'         => 'required|string',
    //         'next_action'   => 'nullable|string',
    //         'followup_date' => 'nullable|date',
    //     ]);

    //     // ✅ SATU-SATUNYA PINTU LOGIC
    //     app(LeadActivityService::class)->log($lead, $data);


    //     return back()->with('success', 'Follow up berhasil ditambahkan.');
    // }


}
