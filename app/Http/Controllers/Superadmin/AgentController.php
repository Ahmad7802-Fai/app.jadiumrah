<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Branch;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $service
    ) {}

    /* =====================================================
     | INDEX — LIST AGENT
     | 🔐 Access: Global Scope + Policy
     ===================================================== */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Agent::class);

        // ===============================
        // HARDENING: VALIDASI CABANG
        // ===============================
        if (
            $request->filled('branch_id') &&
            ! Branch::whereKey($request->branch_id)->exists()
        ) {
            abort(404);
        }

        // ===============================
        // QUERY (GLOBAL SCOPE AUTO)
        // ===============================
        $agents = Agent::query()
            ->with(['user.branch'])
            ->withCount('jamaah')

            // FILTER CABANG
            ->when(
                $request->filled('branch_id'),
                fn ($q) => $q->where('branch_id', $request->branch_id)
            )

            // FILTER STATUS
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('is_active', $request->status)
            )

            // FILTER KEYWORD
            ->when(
                $request->filled('q'),
                function ($q) use ($request) {
                    $q->whereHas('user', function ($u) use ($request) {
                        $u->where('nama', 'like', '%' . $request->q . '%')
                          ->orWhere('email', 'like', '%' . $request->q . '%');
                    });
                }
            )

            ->latest()
            ->paginate(10)
            ->withQueryString();

        $activeBranch = $request->branch_id
            ? Branch::find($request->branch_id)
            : null;

        $branches = Branch::orderBy('nama_cabang')->get();

        return view('superadmin.agent.index', [
            'agents'       => $agents,
            'activeBranch' => $activeBranch,
            'branches'     => $branches,
        ]);
    }

    /* =====================================================
     | CREATE FORM
     ===================================================== */
    public function create(Request $request)
    {
        $this->authorize('create', Agent::class);

        $ctx  = app('access.context');
        $role = strtoupper($ctx['role'] ?? '');

        $branches = collect();

        if (in_array($role, ['SUPERADMIN', 'OPERATOR'])) {
            $branches = Branch::orderBy('nama_cabang')->get();
        }

        return view('superadmin.agent.create', [
            'branches' => $branches,
            'branchId' => $request->branch_id,
        ]);
    }

    /* =====================================================
     | STORE — CREATE AGENT + USER SALES
     ===================================================== */
    public function store(Request $request)
    {
        $this->authorize('create', Agent::class);

        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'email'         => 'required|email',
            'password'      => 'required|min:6',
            'branch_id'     => 'required|exists:branches,id',
            'phone'         => 'nullable|string|max:20',
            'komisi_persen' => 'nullable|numeric|min:0',
        ]);

        $this->service->create($data);

        return redirect()
            ->route('superadmin.agent.index', [
                'branch_id' => $data['branch_id'],
            ])
            ->with('success', 'Agent berhasil dibuat.');
    }

    /* =====================================================
     | EDIT FORM
     ===================================================== */
    public function edit(int $id)
    {
        $agent = Agent::with(['user', 'branch'])->findOrFail($id);

        $this->authorize('update', $agent);

        $ctx  = app('access.context');
        $role = strtoupper($ctx['role'] ?? '');

        $branches = collect();

        if (in_array($role, ['SUPERADMIN', 'OPERATOR'])) {
            $branches = Branch::orderBy('nama_cabang')->get();
        }

        return view('superadmin.agent.edit', [
            'agent'    => $agent,
            'branches' => $branches,
        ]);
    }

    /* =====================================================
     | UPDATE — AGENT + USER
     ===================================================== */
    public function update(Request $request, int $id)
    {
        $agent = Agent::with('user')->findOrFail($id);

        $this->authorize('update', $agent);

        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'email'         => 'required|email',
            'password'      => 'nullable|min:6',
            'phone'         => 'nullable|string|max:20',
            'komisi_persen' => 'nullable|numeric|min:0',
        ]);

        $this->service->update($agent->id, $data);

        return redirect()
            ->route('superadmin.agent.index', [
                'branch_id' => $agent->branch_id,
            ])
            ->with('success', 'Agent berhasil diperbarui.');
    }

    /* =====================================================
     | TOGGLE AKTIF / NONAKTIF
     ===================================================== */
    public function toggle(Agent $agent)
    {
        $this->authorize('toggle', $agent);

        $this->service->toggle($agent->id);

        return back()->with('success', 'Status agent diperbarui.');
    }

    /* =====================================================
     | DELETE AGENT
     ===================================================== */
    public function destroy(Agent $agent)
    {
        $this->authorize('delete', $agent);

        $this->service->delete($agent->id);

        return back()->with('success', 'Agent berhasil dihapus.');
    }


    public function show(Agent $agent, Request $request)
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Chart tahunan
        $omsetPerBulan = $agent->omsetPerBulan($year);

        $chart = [];
        for ($i = 1; $i <= 12; $i++) {
            $chart[] = [
                'month' => $i,
                'omset' => $omsetPerBulan[$i] ?? 0,
            ];
        }

        return view('superadmin.agent.show', [
            'agent'          => $agent->load('user','branch'),
            'year'           => $year,
            'month'          => $month,
            'chartData'      => $chart,
            'totalJamaah'    => $agent->totalJamaah(),
            'totalOmset'     => $agent->totalOmset(),
            'omsetBulanan'   => $agent->omsetBulan($year, $month),
            'jamaahBulanan'  => $agent->jamaahBulan($year, $month),
        ]);
    }

}

// namespace App\Http\Controllers\Superadmin;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\Branch;
// use App\Services\AgentService;
// use Illuminate\Http\Request;

// class AgentController extends Controller
// {
//     public function __construct(
//         protected AgentService $service
//     ) {}

//     /* =====================================================
//      | INDEX — LIST AGENT (FILTER CABANG)
//      ===================================================== */
// public function index(Request $request)
// {
//     $this->authorize('viewAny', Agent::class);

//     // ===============================
//     // HARDENING: VALIDASI CABANG
//     // ===============================
//     if (
//         $request->filled('branch_id') &&
//         ! Branch::whereKey($request->branch_id)->exists()
//     ) {
//         abort(404);
//     }

//     // ===============================
//     // QUERY
//     // ===============================
//     $agents = Agent::query()
//         ->with([
//             'user.branch',
//         ])
//         ->withCount('jamaah') // ✅ TOTAL JAMAAH
//         ->byAccess()

//         // 🔍 FILTER: CABANG
//         ->when(
//             $request->branch_id,
//             fn ($q) => $q->where('branch_id', $request->branch_id)
//         )

//         // 🔍 FILTER: STATUS
//         ->when(
//             $request->filled('status'),
//             fn ($q) => $q->where('is_active', $request->status)
//         )

//         // 🔍 FILTER: KEYWORD (NAMA / EMAIL)
//         ->when(
//             $request->filled('q'),
//             function ($q) use ($request) {
//                 $q->whereHas('user', function ($u) use ($request) {
//                     $u->where('nama', 'like', '%' . $request->q . '%')
//                       ->orWhere('email', 'like', '%' . $request->q . '%');
//                 });
//             }
//         )

//         ->latest()
//         ->paginate(10)
//         ->withQueryString();

//     // ===============================
//     // ACTIVE BRANCH
//     // ===============================
//     $activeBranch = $request->branch_id
//         ? Branch::find($request->branch_id)
//         : null;

//     // ===============================
//     // DATA UNTUK FILTER
//     // ===============================
//     $branches = Branch::orderBy('nama_cabang')->get();

//     return view('superadmin.agent.index', [
//         'agents'       => $agents,
//         'activeBranch' => $activeBranch,
//         'branches'     => $branches,
//     ]);
// }

//     /* =====================================================
//      | CREATE FORM
//      ===================================================== */
//     public function create(Request $request)
//     {
//         $this->authorize('create', Agent::class);

//         $ctx   = app('access.context');
//         $role  = strtoupper($ctx['role'] ?? '');

//         $branches = collect();

//         // SUPERADMIN & OPERATOR boleh pilih cabang
//         if (in_array($role, ['SUPERADMIN','OPERATOR'])) {
//             $branches = Branch::orderBy('nama_cabang')->get();
//         }

//         return view('superadmin.agent.create', [
//             'branches' => $branches,
//             'branchId' => $request->branch_id,
//         ]);
//     }

//     /* =====================================================
//      | STORE — CREATE AGENT + USER SALES
//      ===================================================== */
//     public function store(Request $request)
//     {
//         $this->authorize('create', Agent::class);

//         $data = $request->validate([
//             'nama'          => 'required|string|max:150',
//             'email'         => 'required|email',
//             'password'      => 'required|min:6',
//             'branch_id'     => 'required|exists:branches,id',
//             'phone'         => 'nullable|string|max:20',
//             'komisi_persen' => 'nullable|numeric|min:0',
//         ]);

//         $this->service->create($data);

//         return redirect()
//             ->route('superadmin.agent.index', [
//                 'branch_id' => $data['branch_id'],
//             ])
//             ->with('success', 'Agent berhasil dibuat.');
//     }

//     /* =====================================================
//      | EDIT FORM
//      ===================================================== */
//     public function edit(int $id)
//     {
//         $agent = Agent::with(['user', 'branch'])
//             ->byAccess()
//             ->findOrFail($id);

//         $this->authorize('update', $agent);

//         $ctx   = app('access.context');
//         $role  = strtoupper($ctx['role'] ?? '');

//         $branches = collect();

//         // SUPERADMIN & OPERATOR boleh pindah cabang
//         if (in_array($role, ['SUPERADMIN','OPERATOR'])) {
//             $branches = Branch::orderBy('nama_cabang')->get();
//         }

//         return view('superadmin.agent.edit', [
//             'agent'    => $agent,
//             'branches' => $branches,
//         ]);
//     }

//     /* =====================================================
//      | UPDATE — AGENT + USER
//      ===================================================== */
//     public function update(Request $request, int $id)
//     {
//         $agent = Agent::with('user')
//             ->byAccess()
//             ->findOrFail($id);

//         $this->authorize('update', $agent);

//         $data = $request->validate([
//             'nama'          => 'required|string|max:150',
//             'email'         => 'required|email',
//             'password'      => 'nullable|min:6',
//             'phone'         => 'nullable|string|max:20',
//             'komisi_persen' => 'nullable|numeric|min:0',
//         ]);

//         // ❗ branch & kode_agent dikunci (tidak dari controller)
//         $this->service->update($agent->id, $data);

//         return redirect()
//             ->route('superadmin.agent.index', [
//                 'branch_id' => $agent->branch_id,
//             ])
//             ->with('success', 'Agent berhasil diperbarui.');
//     }

//     /* =====================================================
//      | TOGGLE AKTIF / NONAKTIF
//      ===================================================== */
//     public function toggle(Agent $agent)
//     {
//         $this->authorize('toggle', $agent);

//         $this->service->toggle($agent->id);

//         return back()->with('success', 'Status agent diperbarui.');
//     }

//     /* =====================================================
//      | DELETE AGENT (STRICT)
//      ===================================================== */
//     public function destroy(Agent $agent)
//     {
//         $this->authorize('delete', $agent);

//         $this->service->delete($agent->id);

//         return back()->with('success', 'Agent berhasil dihapus.');
//     }
// }


// namespace App\Http\Controllers\Superadmin;

// use App\Http\Controllers\Controller;
// use App\Models\Agent;
// use App\Models\Branch;
// use App\Services\AgentService;
// use Illuminate\Http\Request;

// class AgentController extends Controller
// {
//     public function __construct(
//         protected AgentService $service
//     ) {}

//     /* =====================================================
//      | INDEX — LIST AGENT (FILTER BY CABANG)
//      ===================================================== */
//     public function index(Request $request)
//     {
//         $this->authorize('viewAny', Agent::class);

//         // Optional hardening
//         if ($request->branch_id && ! Branch::whereKey($request->branch_id)->exists()) {
//             abort(404);
//         }

//         $agents = Agent::with(['user.branch'])
//             ->when($request->branch_id, fn ($q) =>
//                 $q->where('branch_id', $request->branch_id)
//             )
//             ->latest()
//             ->paginate(10)
//             ->withQueryString();

//         $activeBranch = $request->branch_id
//             ? Branch::find($request->branch_id)
//             : null;

//         return view('superadmin.agent.index', compact(
//             'agents',
//             'activeBranch'
//         ));
//     }

//     /* =====================================================
//      | CREATE FORM
//      ===================================================== */
//     public function create(Request $request)
//     {
//         $this->authorize('create', Agent::class);

//         $ctx   = app('access.context');
//         $role  = strtoupper($ctx['role'] ?? '');

//         $branches = collect();
//         $branchId = $request->branch_id;

//         // SUPERADMIN / OPERATOR boleh pilih cabang
//         if (in_array($role, ['SUPERADMIN', 'OPERATOR'])) {
//             $branches = Branch::orderBy('nama_cabang')->get();
//         }

//         return view('superadmin.agent.create', [
//             'branches' => $branches,
//             'branchId' => $branchId,
//         ]);
//     }

//     /* =====================================================
//      | STORE — CREATE AGENT (AUTO USER SALES)
//      ===================================================== */
//     public function store(Request $request)
//     {
//         $this->authorize('create', Agent::class);

//         $data = $request->validate([
//             'nama'           => 'required|string|max:150',
//             'email'          => 'required|email',
//             'password'       => 'required|min:6',
//             'branch_id'      => 'required|exists:branches,id',
//             'phone'          => 'nullable|string|max:20',
//             'komisi_persen'  => 'nullable|numeric|min:0',
//         ]);

//         $this->service->create($data);

//         return redirect()
//             ->route('superadmin.agent.index', [
//                 'branch_id' => $data['branch_id']
//             ])
//             ->with('success', 'Agent berhasil dibuat.');
//     }

//     /* =====================================================
//      | EDIT FORM
//      ===================================================== */
//     public function edit(int $id)
//     {
//         $agent = Agent::with(['user.branch'])
//             ->byAccess()
//             ->findOrFail($id);

//         $this->authorize('update', $agent);

//         $ctx  = app('access.context');
//         $role = strtoupper($ctx['role'] ?? '');

//         $branches = collect();

//         // SUPERADMIN / OPERATOR boleh pindah cabang
//         if (in_array($role, ['SUPERADMIN', 'OPERATOR'])) {
//             $branches = Branch::orderBy('nama_cabang')->get();
//         }

//         return view('superadmin.agent.edit', [
//             'agent'    => $agent,
//             'branches' => $branches,
//         ]);
//     }

//     /* =====================================================
//      | UPDATE — AGENT + USER
//      ===================================================== */
//     public function update(Request $request, int $id)
//     {
//         $agent = Agent::with('user')
//             ->byAccess()
//             ->findOrFail($id);

//         $this->authorize('update', $agent);

//         $data = $request->validate([
//             'nama'           => 'required|string|max:150',
//             'email'          => 'required|email',
//             'password'       => 'nullable|min:6',
//             'phone'          => 'nullable|string|max:20',
//             'komisi_persen'  => 'nullable|numeric|min:0',
//         ]);

//         // ❗ Tidak ada branch_id & kode_agent di controller
//         $this->service->update($agent->id, $data);

//         return redirect()
//             ->route('superadmin.agent.index', [
//                 'branch_id' => $agent->branch_id
//             ])
//             ->with('success', 'Agent berhasil diperbarui.');
//     }

//     /* =====================================================
//      | TOGGLE AKTIF / NONAKTIF
//      ===================================================== */
//     public function toggle(Agent $agent)
//     {
//         $this->authorize('toggle', $agent);

//         $this->service->toggle($agent->id);

//         return back()->with('success', 'Status agent diperbarui.');
//     }

//     /* =====================================================
//      | DELETE AGENT
//      ===================================================== */
//     public function destroy(Agent $agent)
//     {
//         $this->authorize('delete', $agent);

//         $this->service->delete($agent->id);

//         return back()->with('success', 'Agent berhasil dihapus.');
//     }
// }
