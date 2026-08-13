<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\Keberangkatan;
use App\Models\Agent;
use App\Services\JamaahService;
use App\Services\WhatsAppLinkService;

use Illuminate\Http\Request;

class JamaahController extends Controller
{
    protected int $agentId;

    public function __construct(
        protected JamaahService $jamaahService,
        protected WhatsAppLinkService $wa
    ) {
        // 🔐 Pastikan benar-benar AGENT
        $this->middleware(function ($request, $next) {

            $user = auth()->user();

            abort_unless($user && $user->role === 'SALES', 403);

            // 🔥 AMBIL AGENT_ID LANGSUNG (ANTI NULL RELATION)
            $agentId = Agent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->value('id');

            abort_unless($agentId, 403);

            // simpan untuk dipakai semua method
            $this->agentId = (int) $agentId;

            return $next($request);
        });
    }

    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $query = Jamaah::withoutGlobalScopes()
            ->where('agent_id', $this->agentId);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($s) use ($q) {
                $s->where('nama_lengkap', 'like', "%{$q}%")
                  ->orWhere('nik', 'like', "%{$q}%")
                  ->orWhere('no_hp', 'like', "%{$q}%")
                  ->orWhere('no_id', 'like', "%{$q}%");
            });
        }

        $jamaah = $query->latest()->paginate(15)->withQueryString();

        return view('agent.jamaah.index', compact('jamaah'));
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        return view('agent.jamaah.create', [
            'jamaah'        => null,
            'keberangkatan' => Keberangkatan::where('status', 'Aktif')->get(),
            'autoNoID'      => $this->jamaahService->generateNoId(),
        ]);
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        $data = $request->except(['_token', 'agent_id']);
        $data['agent_id'] = $this->agentId;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto');
        }

        $this->jamaahService->create($data);

        return redirect()
            ->route('agent.jamaah.index')
            ->with('success', 'Jamaah berhasil ditambahkan.');
    }

    /* =====================================================
     | SHOW
     ===================================================== */
    public function show(int $id)
    {
        $jamaah = $this->findOwnedJamaahOrFail($id);

        return view('agent.jamaah.show', compact('jamaah'));
    }

    /* =====================================================
     | EDIT
     ===================================================== */
    public function edit(int $id)
    {
        $jamaah = $this->findOwnedJamaahOrFail($id);

        return view('agent.jamaah.edit', [
            'jamaah'        => $jamaah,
            'keberangkatan' => Keberangkatan::where('status', 'Aktif')->get(),
        ]);
    }

    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, int $id)
    {
        $jamaah = $this->findOwnedJamaahOrFail($id);

        $this->jamaahService->update(
            $jamaah->id,
            $request->except(['agent_id'])
        );

        return redirect()
            ->route('agent.jamaah.index')
            ->with('success', 'Data jamaah berhasil diperbarui.');
    }

    /* =====================================================
     | PRIVATE — JAMAAH MILIK AGENT
     ===================================================== */
    private function findOwnedJamaahOrFail(int $id): Jamaah
    {
        return Jamaah::withoutGlobalScopes()
            ->where('id', $id)
            ->where('agent_id', $this->agentId)
            ->firstOrFail();
    }

    /* =====================================================
    | WHATSAPP LINK
    ===================================================== */

public function whatsapp(
    int $id,
    \Illuminate\Http\Request $request,
    WhatsAppLinkService $wa
) {
    $jamaah = $this->findOwnedJamaahOrFail($id);

    $type = $request->get('type', 'follow_up');

    return redirect()->away(
        $wa->generate($jamaah, $type)
    );
}

}
