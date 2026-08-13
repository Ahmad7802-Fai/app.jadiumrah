<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Agent;
use App\Services\Lead\LeadService;
use App\Services\Lead\LeadActivityService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    protected int $agentId;

    public function __construct(
        protected LeadService $leadService,
        protected LeadActivityService $activityService
    ) {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            abort_unless($user && $user->isAgent(), 403);

            $this->agentId = $user->agent->id;

            return $next($request);
        });
    }

    /* ===============================
     | INDEX
     =============================== */
    public function index(Request $request)
    {
        $leads = Lead::where('agent_id', $this->agentId)
            ->when($request->q, fn ($q) =>
                $q->where(function ($w) use ($request) {
                    $w->where('nama', 'like', "%{$request->q}%")
                      ->orWhere('no_hp', 'like', "%{$request->q}%");
                })
            )
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('agent.leads.index', compact('leads'));
    }

    /* ===============================
     | CREATE
     =============================== */
    public function create()
    {
        return view('agent.leads.create', [
            'sources' => LeadSource::orderBy('nama_sumber')->get(),
        ]);
    }

    /* ===============================
     | STORE
     =============================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'no_hp'     => 'required|string|max:50',
            'email'     => 'nullable|email',
            'sumber_id' => 'required|exists:lead_sources,id',
            'channel'   => 'required|in:online,offline',
            'catatan'   => 'nullable|string',
        ]);

        $this->leadService->create($data, auth()->user());

        return redirect()
            ->route('agent.leads.index')
            ->with('success', 'Lead berhasil ditambahkan.');
    }

    /* ===============================
     | SHOW
     =============================== */
    public function show(int $id)
    {
        $lead = $this->findOwnedLeadOrFail($id);

        return view('agent.leads.show', [
            'lead' => $lead->load('activities'),
        ]);
    }

    /* ===============================
     | EDIT
     =============================== */
    public function edit(int $id)
    {
        $lead = $this->findOwnedLeadOrFail($id);

        return view('agent.leads.edit', [
            'lead'    => $lead,
            'sources' => LeadSource::orderBy('nama_sumber')->get(),
        ]);
    }

    /* ===============================
     | UPDATE
     =============================== */
    public function update(Request $request, int $id)
    {
        $lead = $this->findOwnedLeadOrFail($id);

        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'no_hp'     => 'required|string|max:50',
            'email'     => 'nullable|email',
            'sumber_id' => 'required|exists:lead_sources,id',
            'channel'   => 'required|in:online,offline',
            'catatan'   => 'nullable|string',
        ]);

        $this->leadService->update($lead, $data, auth()->user());

        return redirect()
            ->route('agent.leads.show', $lead->id)
            ->with('success', 'Lead berhasil diperbarui.');
    }

    /* ===============================
     | FOLLOW UP
     =============================== */
    public function followupStore(Request $request, int $id)
    {
        $lead = $this->findOwnedLeadOrFail($id);

        $data = $request->validate([
            'aktivitas'     => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi,followup,closing',
            'hasil'         => 'nullable|string',
            'next_action'   => 'nullable|string',
            'followup_date' => 'nullable|date',
        ]);

        $this->activityService->store($lead, $data);

        return back()->with('success', 'Follow up berhasil disimpan.');
    }

    /* ===============================
     | SECURITY
     =============================== */
    private function findOwnedLeadOrFail(int $id): Lead
    {
        return Lead::where('id', $id)
            ->where('agent_id', $this->agentId)
            ->firstOrFail();
    }
}

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\LeadSource;
// use App\Models\Agent;
// use App\Services\Lead\LeadService;
// use App\Services\Lead\LeadActivityService;
// use Illuminate\Http\Request;

// class LeadController extends Controller
// {
//     protected int $agentId;

//     public function __construct(
//         protected LeadService $leadService,
//         protected LeadActivityService $activityService
//     ) {
//         $this->middleware(function ($request, $next) {

//             $user = auth()->user();
//             abort_unless($user && $user->role === 'SALES', 403);

//             $agentId = Agent::withoutGlobalScopes()
//                 ->where('user_id', $user->id)
//                 ->value('id');

//             abort_unless($agentId, 403);

//             $this->agentId = (int) $agentId;

//             return $next($request);
//         });
//     }

//     /* ===============================
//      | INDEX
//      =============================== */
//     public function index(Request $request)
//     {
//         $leads = Lead::withoutGlobalScopes()
//             ->where('agent_id', $this->agentId)
//             ->when($request->q, fn ($q) =>
//                 $q->where('nama', 'like', "%{$request->q}%")
//                   ->orWhere('no_hp', 'like', "%{$request->q}%")
//             )
//             ->when($request->status, fn ($q) =>
//                 $q->where('status', $request->status)
//             )
//             ->latest()
//             ->paginate(15);

//         return view('agent.leads.index', compact('leads'));
//     }

//     /* ===============================
//      | CREATE
//      =============================== */
//     public function create()
//     {
//         return view('agent.leads.create', [
//             'sources' => LeadSource::orderBy('nama_sumber')->get(),
//         ]);
//     }

//     /* ===============================
//      | STORE
//      =============================== */
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'nama'      => 'required|string|max:255',
//             'no_hp'     => 'required|string|max:50',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|exists:lead_sources,id',
//             'channel'   => 'required|in:online,offline',
//             'catatan'   => 'nullable|string',
//         ]);

//         // 🔥 PAKAI YANG SUDAH TERUJI
//         $this->leadService->create($data, auth()->user());

//         return redirect()
//             ->route('agent.leads.index')
//             ->with('success', 'Lead berhasil ditambahkan.');
//     }

//     /* ===============================
//      | SHOW
//      =============================== */
//     public function show(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         return view('agent.leads.show', [
//             'lead' => $lead->load('activities'),
//         ]);
//     }

//     /* ===============================
//      | EDIT
//      =============================== */
//     public function edit(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         return view('agent.leads.edit', [
//             'lead'    => $lead,
//             'sources' => LeadSource::orderBy('nama_sumber')->get(),
//         ]);
//     }

//     /* ===============================
//      | UPDATE (TANPA STATUS)
//      =============================== */
//     public function update(Request $request, int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         $data = $request->validate([
//             'nama'      => 'required|string|max:255',
//             'no_hp'     => 'required|string|max:50',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|exists:lead_sources,id',
//             'channel'   => 'required|in:online,offline',
//             'catatan'   => 'nullable|string',
//         ]);

//         $lead->update($data);

//         return redirect()
//             ->route('agent.leads.show', $lead->id)
//             ->with('success', 'Lead berhasil diperbarui.');
//     }

//     /* ===============================
//      | FOLLOW UP (INTI CRM)
//      =============================== */
//     public function followupStore(Request $request, int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         $data = $request->validate([
//             'aktivitas'     => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi,followup,closing',
//             'hasil'         => 'nullable|string',
//             'next_action'   => 'nullable|string',
//             'followup_date' => 'nullable|date',
//         ]);

//         // 🔥 SATU-SATUNYA CARA UBAH STATUS
//         $this->activityService->store($lead, $data);

//         return back()->with('success', 'Follow up berhasil disimpan.');
//     }

//     /* ===============================
//      | DELETE
//      =============================== */
//     public function destroy(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);
//         $lead->delete();

//         return redirect()
//             ->route('agent.leads.index')
//             ->with('success', 'Lead berhasil dihapus.');
//     }

//     /* ===============================
//      | SECURITY
//      =============================== */
//     private function findOwnedLeadOrFail(int $id): Lead
//     {
//         return Lead::withoutGlobalScopes()
//             ->where('id', $id)
//             ->where('agent_id', $this->agentId)
//             ->firstOrFail();
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\LeadSource;
// use App\Models\Agent;
// use App\Services\Lead\LeadService;
// use App\Services\Lead\LeadActivityService;
// use Illuminate\Http\Request;

// class LeadController extends Controller
// {
//     protected int $agentId;

//     public function __construct(
//         protected LeadService $leadService,
//         protected LeadActivityService $activityService
//     ) {
//         $this->middleware(function ($request, $next) {

//             $user = auth()->user();
//             abort_unless($user && $user->role === 'SALES', 403);

//             $agentId = Agent::withoutGlobalScopes()
//                 ->where('user_id', $user->id)
//                 ->value('id');

//             abort_unless($agentId, 403);

//             $this->agentId = (int) $agentId;

//             return $next($request);
//         });
//     }

//     /* =====================================================
//      | INDEX — LIST LEAD
//      ===================================================== */
//     public function index(Request $request)
//     {
//         $leads = Lead::withoutGlobalScopes()
//             ->where('agent_id', $this->agentId)
//             ->when($request->q, fn ($q) =>
//                 $q->where('nama', 'like', "%{$request->q}%")
//                   ->orWhere('no_hp', 'like', "%{$request->q}%")
//             )
//             ->when($request->status, fn ($q) =>
//                 $q->where('status', $request->status)
//             )
//             ->latest()
//             ->paginate(15);

//         return view('agent.leads.index', compact('leads'));
//     }

//     /* =====================================================
//      | CREATE — FORM TAMBAH LEAD
//      ===================================================== */
//     public function create()
//     {
//         return view('agent.leads.create', [
//             'sources' => LeadSource::orderBy('nama_sumber')->get(),
//         ]);
//     }

//     /* =====================================================
//      | STORE — SIMPAN LEAD BARU
//      ===================================================== */
//     public function store(Request $request)
//     {
//         $data = $request->validate([
//             'nama'      => 'required|string|max:255',
//             'no_hp'     => 'required|string|max:50',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|exists:lead_sources,id',
//             'channel'   => 'required|in:online,offline',
//             'catatan'   => 'nullable|string',
//         ]);

//         $this->leadService->createSimple($data, $this->agentId);

//         return redirect()
//             ->route('agent.leads.index')
//             ->with('success', 'Lead berhasil ditambahkan.');
//     }

//     /* =====================================================
//      | SHOW — DETAIL LEAD + FOLLOW UP LOG
//      ===================================================== */
//     public function show(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         return view('agent.leads.show', [
//             'lead' => $lead->load([
//                 'activities' => fn ($q) => $q->latest(),
//             ]),
//         ]);
//     }

//     /* =====================================================
//      | EDIT — FORM EDIT LEAD
//      ===================================================== */
//     public function edit(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         return view('agent.leads.edit', [
//             'lead'    => $lead,
//             'sources' => LeadSource::orderBy('nama_sumber')->get(),
//         ]);
//     }

//     /* =====================================================
//      | UPDATE — SIMPAN PERUBAHAN LEAD
//      ===================================================== */
//     public function update(Request $request, int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         $data = $request->validate([
//             'nama'      => 'required|string|max:255',
//             'no_hp'     => 'required|string|max:50',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|exists:lead_sources,id',
//             'channel'   => 'required|in:online,offline',
//             'status'    => 'required|in:NEW,FOLLOWUP,CLOSED,LOST',
//             'catatan'   => 'nullable|string',
//         ]);

//         $lead->update($data);

//         return redirect()
//             ->route('agent.leads.show', $lead->id)
//             ->with('success', 'Lead berhasil diperbarui.');
//     }

//     /* =====================================================
//      | DELETE — HAPUS LEAD
//      ===================================================== */
//     public function destroy(int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);
//         $lead->delete();

//         return redirect()
//             ->route('agent.leads.index')
//             ->with('success', 'Lead berhasil dihapus.');
//     }

//     /* =====================================================
//      | FOLLOW UP — SATU-SATUNYA INTERAKSI CRM
//      ===================================================== */
//     public function followupStore(Request $request, int $id)
//     {
//         $lead = $this->findOwnedLeadOrFail($id);

//         $data = $request->validate([
//             'aktivitas'     => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi',
//             'hasil'         => 'nullable|string',
//             'next_action'   => 'nullable|string',
//             'followup_date' => 'nullable|date',
//         ]);

//         $this->activityService->store($lead, $data);

//         // 🔥 otomatis naik status
//         if ($lead->status === 'NEW') {
//             $lead->update(['status' => 'FOLLOWUP']);
//         }

//         return back()->with('success', 'Follow up berhasil disimpan.');
//     }

//     /* =====================================================
//      | PRIVATE — SECURITY GUARD
//      ===================================================== */
//     private function findOwnedLeadOrFail(int $id): Lead
//     {
//         return Lead::withoutGlobalScopes()
//             ->where('id', $id)
//             ->where('agent_id', $this->agentId)
//             ->firstOrFail();
//     }
// }

