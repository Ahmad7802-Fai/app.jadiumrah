<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Pipeline;
use App\Services\Lead\LeadService;
use App\Services\Lead\LeadClosingService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $leads = $this->leadService->paginate($request);

        return view('cabang.leads.index', compact('leads'));
    }

    /* =====================================================
     | CREATE FORM
     ===================================================== */
    public function create()
    {
        $sources = LeadSource::orderBy('nama_sumber')->get();

        return view('cabang.leads.create', compact('sources'));
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'no_hp'     => 'required|string|max:50',
            'email'     => 'nullable|email',
            'sumber_id' => 'required|integer',
            'channel'   => 'required|in:offline,online',
            'catatan'   => 'nullable|string',
        ]);

        $this->leadService->create($data, auth()->user());

        return redirect()
            ->route('cabang.leads.index')
            ->with('success', 'Lead berhasil ditambahkan.');
    }

    /* =====================================================
     | SHOW
     ===================================================== */
    public function show(Lead $lead)
    {
        $lead->load([
            'source',
            'agent',
            'activities.user',
            'closing',
        ]);

        $pipelines = Pipeline::where('aktif', 1)
            ->orderBy('urutan')
            ->get();

        return view('cabang.leads.show', compact('lead', 'pipelines'));
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit(Lead $lead)
    {
        abort_if($lead->status === 'CLOSED', 403);

        $sources = LeadSource::orderBy('nama_sumber')->get();

        return view('cabang.leads.edit', compact('lead','sources'));
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, Lead $lead)
    {
        abort_if($lead->status === 'CLOSED', 403);

        $data = $request->validate([
            'nama'    => 'required|string|max:255',
            'no_hp'   => 'required|string|max:50',
            'email'   => 'nullable|email',
            'catatan' => 'nullable|string',
        ]);

        $this->leadService->update($lead, $data, auth()->user());

        return redirect()
            ->route('cabang.leads.index')
            ->with('success', 'Lead diperbarui.');
    }

    /* =====================================================
     | SUBMIT CLOSING
     ===================================================== */
    public function submitClosing(
        Lead $lead,
        LeadClosingService $closingService
    ) {
        abort_unless(
            auth()->user()->isPusat()
            || $lead->branch_id === auth()->user()->branch_id,
            403
        );

        $closingService->submit($lead);

        return back()->with(
            'success',
            'Closing berhasil diajukan. Menunggu approval pusat.'
        );
    }
}

// namespace App\Http\Controllers\Cabang;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\LeadSource;
// use App\Services\Lead\LeadService;
// use App\Services\Lead\LeadClosingService;
// use App\Models\Pipeline;
// use Illuminate\Http\Request;

// class LeadController extends Controller
// {
//     public function __construct(
//         protected LeadService $leadService
//     ) {}

//     /* =====================================================
//      | INDEX
//      ===================================================== */
//     public function index(Request $request)
//     {
//         // 🔐 scope otomatis dari AccessScope
//         $leads = $this->leadService->paginate($request);

//         return view('cabang.leads.index', compact('leads'));
//     }

//     /* =====================================================
//      | CREATE FORM
//      ===================================================== */
//     public function create()
//     {
//         $sources = LeadSource::orderBy('nama_sumber')->get();

//         return view('cabang.leads.create', compact('sources'));
//     }

//     /* =====================================================
//      | STORE
//      ===================================================== */
//     public function store(Request $request)
//     {
//         $ctx = app('access.context');

//         $data = $request->validate([
//             'nama'      => 'required|string|max:255',
//             'no_hp'     => 'required|string|max:50',
//             'email'     => 'nullable|email',
//             'sumber_id' => 'required|integer',
//             'channel'   => 'required|in:offline,online',
//             'catatan'   => 'nullable|string',
//         ]);

//         $this->leadService->create([
//             ...$data,
//             'branch_id' => $ctx['branch_id'],
//             'agent_id'  => $ctx['agent_id'] ?? null,
//         ], auth()->user());

//         return redirect()
//             ->route('cabang.leads.index')
//             ->with('success', 'Lead berhasil ditambahkan.');
//     }

//     /* =====================================================
//      | SHOW
//      ===================================================== */

//     public function show(Lead $lead)
//     {
//         // ❌ hapus authorizeCabang()

//         $lead->load([
//             'sumber',
//             'agent',
//             'activities.user',
//             'closing',
//         ]);

//         $pipelines = Pipeline::where('aktif', 1)
//             ->orderBy('urutan')
//             ->get();

//         return view('cabang.leads.show', compact('lead'));
//     }


//     /* =====================================================
//      | EDIT
//      ===================================================== */
//     public function edit(Lead $lead)
//     {
//         abort_if($lead->status === 'CLOSED', 403);

//         $sources = LeadSource::orderBy('nama_sumber')->get();

//         return view('cabang.leads.edit', compact('lead','sources'));
//     }

//     /* =====================================================
//      | UPDATE
//      ===================================================== */
//     public function update(Request $request, Lead $lead)
//     {
//         abort_if($lead->status === 'CLOSED', 403);

//         $data = $request->validate([
//             'nama'    => 'required|string|max:255',
//             'no_hp'   => 'required|string|max:50',
//             'email'   => 'nullable|email',
//             'catatan' => 'nullable|string',
//         ]);

//         $this->leadService->update($lead, $data, auth()->user());

//         return redirect()
//             ->route('cabang.leads.index')
//             ->with('success', 'Lead diperbarui.');
//     }

//     public function submitClosing(
//         Lead $lead,
//         LeadClosingService $closingService
//     ) {
//         $ctx = app('access.context');

//         // 🔐 Pastikan lead milik cabang
//         abort_unless(
//             $lead->branch_id === $ctx['branch_id'],
//             403
//         );

//         $closingService->submit($lead);

//         return back()->with(
//             'success',
//             'Closing berhasil diajukan. Menunggu approval pusat.'
//         );
//     }

// }
