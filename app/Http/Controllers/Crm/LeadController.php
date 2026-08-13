<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Pipeline;
use App\Services\Lead\LeadService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    /* =====================================================
     | INDEX — LIST LEAD (PUSAT)
     ===================================================== */
    public function index(Request $request)
    {
        $this->authorizePusat();

        $leads = $this->leadService->paginate($request);

        return view('crm.leads.index', compact('leads'));
    }

    /* =====================================================
     | SHOW — DETAIL LEAD
     ===================================================== */

    public function show(Lead $lead)
    {
        // $pipelines = Pipeline::where('aktif', 1)
        //     ->orderBy('urutan')
        //     ->get();

            $lead->load([
                'sumber',
                'agent',
                'activities.user',
                'latestFollowUp',
                'closing',
            ]);


        return view('crm.leads.show', compact(
            'lead',
            // 'pipelines'
        ));
    }



    /* =====================================================
     | CREATE FORM
     ===================================================== */
    public function create()
    {
        $this->authorizePusat();

        $sources = LeadSource::orderBy('nama_sumber')->get();

        return view('crm.leads.create', compact('sources'));
    }

    /* =====================================================
     | STORE — CREATE LEAD
     ===================================================== */
    public function store(Request $request)
    {
        $this->authorizePusat();

        $data = $request->validate([
            'nama'        => 'required|string|max:255',
            'no_hp'       => 'required|string|max:50',
            'email'       => 'nullable|email',
            'sumber_id'   => 'required|integer',
            'channel'     => 'required|in:offline,online',
            'pipeline_id' => 'nullable|integer',
            'catatan'     => 'nullable|string',
        ]);

        $this->leadService->create($data, auth()->user());

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil ditambahkan.');
    }

    /* =====================================================
     | EDIT FORM
     ===================================================== */
    public function edit(Lead $lead)
    {
        $this->authorizePusat();

        $sources = LeadSource::orderBy('nama_sumber')->get();


        return view('crm.leads.edit', compact('lead','sources'));
    }


    /* =====================================================
     | UPDATE LEAD
     ===================================================== */
    public function update(Request $request, Lead $lead)
    {
        $this->authorizePusat();

        $data = $request->validate([
            'nama'        => 'required|string|max:255',
            'no_hp'       => 'required|string|max:50',
            'email'       => 'nullable|email',
            'pipeline_id' => 'nullable|integer',
            'catatan'     => 'nullable|string',
        ]);

        $this->leadService->update($lead, $data, auth()->user());

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil diperbarui.');
    }

    /* =====================================================
     | DELETE (SUPERADMIN ONLY)
     ===================================================== */
    public function destroy(Lead $lead)
    {
        $this->authorizePusat();

        $this->leadService->delete($lead, auth()->user());

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }

    /* =====================================================
     | INTERNAL AUTH
     ===================================================== */
    private function authorizePusat(): void
    {
        abort_unless(
            auth()->user()?->isPusat(),
            403,
            'Akses khusus pusat.'
        );
    }

}

// namespace App\Http\Controllers\Crm;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// use App\Models\Lead;
// use App\Models\Pipeline;
// use App\Models\PipelineLog;
// use App\Models\LeadSource;

// class LeadController extends Controller
// {
//     /* =====================================================
//      | LIST LEAD
//      ===================================================== */
//     public function index(Request $request)
//     {
//         $leads = Lead::with(['pipeline', 'source'])
//             ->latest()
//             ->paginate(20);

//         $pipelines = Pipeline::orderBy('urutan')->get();

//         return view('crm.leads.index', compact('leads', 'pipelines'));
//     }

//     /* =====================================================
//      | CREATE
//      ===================================================== */
//     public function create()
//     {
//         $sources = LeadSource::orderBy('nama_sumber')->get();

//         return view('crm.leads.create', compact('sources'));
//     }

//     /* =====================================================
//      | STORE
//      ===================================================== */
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'nama'      => 'required|string|max:150',
//             'no_hp'     => 'required|string|max:30',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|exists:lead_sources,id',
//             'channel'   => 'required|in:online,offline',
//             'catatan'   => 'nullable|string',
//         ]);

//         // Branch otomatis
//         $data['branch_id'] = auth()->user()->branch_id
//             ?? config('crm.default_branch_id');

//         Lead::create($data);

//         return redirect()
//             ->route('crm.leads.index')
//             ->with('success', 'Lead berhasil ditambahkan.');
//     }

//     /* =====================================================
//      | SHOW
//      ===================================================== */
//     public function show(Lead $lead)
//     {
//         $lead->load([
//             'source',
//             'pipeline',
//             'closing.agent',
//             'pipelineLogs.user',
//             'activities.user',
//         ]);

//         $pipelines = Pipeline::where('aktif', 1)
//             ->orderBy('urutan')
//             ->get();

//         return view('crm.leads.show', compact('lead', 'pipelines'));
//     }

//     /* =====================================================
//      | EDIT
//      ===================================================== */
//     public function edit(Lead $lead)
//     {
//         $sources = LeadSource::orderBy('nama_sumber')->get();

//         return view('crm.leads.edit', compact('lead', 'sources'));
//     }

//     /* =====================================================
//      | UPDATE
//      ===================================================== */
//     public function update(Request $request, Lead $lead)
//     {
//         $data = $request->validate([
//             'nama'  => 'required|string',
//             'no_hp' => 'required|string',
//             'email' => 'nullable|email',
//         ]);

//         $lead->update($data);

//         return redirect()
//             ->route('crm.leads.show', $lead)
//             ->with('success', 'Lead berhasil diperbarui.');
//     }

//     /* =====================================================
//      | UPDATE PIPELINE
//      ===================================================== */
//     public function updatePipeline(Request $request, Lead $lead)
//     {
//         $data = $request->validate([
//             'pipeline_id' => 'required|exists:pipelines,id',
//         ]);

//         if ($lead->pipeline_id == $data['pipeline_id']) {
//             return back()->with('info', 'Pipeline tidak berubah.');
//         }

//         $oldPipeline = $lead->pipeline_id;
//         $newPipeline = $data['pipeline_id'];

//         $lead->update([
//             'pipeline_id' => $newPipeline,
//             'status'      => optional(
//                 Pipeline::find($newPipeline)
//             )->tahap,
//         ]);

//         PipelineLog::create([
//             'lead_id'            => $lead->id,
//             'from_pipeline_id'   => $oldPipeline,
//             'to_pipeline_id'     => $newPipeline,
//             'from_pipeline_name' => optional(Pipeline::find($oldPipeline))->tahap,
//             'to_pipeline_name'   => optional(Pipeline::find($newPipeline))->tahap,
//             'action'             => 'PIPELINE_UPDATE',
//             'created_by'         => Auth::id(),
//             'changed_at'         => now(),
//         ]);

//         return back()->with('success', 'Pipeline berhasil diperbarui.');
//     }
// }
