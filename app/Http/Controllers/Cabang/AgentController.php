<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Exception;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $service
    ) {
        if (auth()->check() && auth()->user()->role !== 'ADMIN') {
            abort(403);
        }
    }
    /* =====================================================
     | INDEX — LIST AGENT CABANG
     ===================================================== */
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id;

        $agents = Agent::with(['user'])
            ->where('branch_id', $branchId)
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where('kode_agent', 'like', "%{$request->q}%")
                  ->orWhereHas('user', function ($u) use ($request) {
                      $u->where('nama', 'like', "%{$request->q}%")
                        ->orWhere('email', 'like', "%{$request->q}%");
                  });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', (int) $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('cabang.agent.index', compact('agents'));
    }

    /* =====================================================
     | CREATE FORM
     ===================================================== */
    public function create()
    {
        return view('cabang.agent.create');
    }

    /* =====================================================
     | STORE — CREATE AGENT + USER SALES
     ===================================================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'email'         => 'required|email',
            'password'      => 'required|min:6',
            'phone'         => 'nullable|string|max:20',
            'komisi_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $this->service->create($data);

            return redirect()
                ->route('cabang.agent.index')
                ->with('success', 'Agent berhasil ditambahkan.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'nama'          => 'required|string|max:150',
    //         'email'         => 'required|email',
    //         'password'      => 'required|min:6',
    //         'phone'         => 'nullable|string|max:20',
    //         'komisi_persen' => 'nullable|numeric|min:0|max:100',
    //     ]);

    //     try {
    //         $this->service->create([
    //             ...$data,
    //             'branch_id' => auth()->user()->branch_id,
    //         ]);

    //         return redirect()
    //             ->route('cabang.agent.index')
    //             ->with('success', 'Agent berhasil ditambahkan.');

    //     } catch (Exception $e) {
    //         return back()
    //             ->withInput()
    //             ->with('error', $e->getMessage());
    //     }
    // }

    /* =====================================================
     | EDIT FORM
     ===================================================== */
    public function edit(Agent $agent)
    {
        $this->ensureSameBranch($agent);

        return view('cabang.agent.edit', compact('agent'));
    }

    /* =====================================================
     | UPDATE — AGENT + USER
     ===================================================== */
    public function update(Request $request, Agent $agent)
    {
        $this->ensureSameBranch($agent);

        $data = $request->validate([
            'nama'          => 'required|string|max:150',
            'email'         => 'required|email',
            'password'      => 'nullable|min:6',
            'phone'         => 'nullable|string|max:20',
            'komisi_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $this->service->update($agent->id, $data);

            return redirect()
                ->route('cabang.agent.index')
                ->with('success', 'Agent berhasil diperbarui.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     | TOGGLE — AKTIF / NONAKTIF
     ===================================================== */
    public function toggle(Agent $agent)
    {
        $this->ensureSameBranch($agent);

        try {
            $this->service->toggle($agent->id);

            return back()
                ->with('success', 'Status agent berhasil diubah.');

        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     | DELETE — STRICT
     ===================================================== */
    public function destroy(Agent $agent)
    {
        $this->ensureSameBranch($agent);

        try {
            $this->service->delete($agent->id);

            return back()
                ->with('success', 'Agent berhasil dihapus.');

        } catch (Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     | GUARD — PASTIKAN CABANG SAMA
     ===================================================== */
    private function ensureSameBranch(Agent $agent): void
    {
        if ($agent->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Akses ditolak.');
        }
    }

    /* =====================================================
    | SHOW — DETAIL AGENT CABANG
    ===================================================== */
    public function show(Agent $agent)
    {
        $this->ensureSameBranch($agent);

        $agent->loadCount([
            'jamaah',
            'leads'
        ]);

        return view('cabang.agent.show', compact('agent'));
    }

}



