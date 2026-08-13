<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Jamaah\ManualJamaahService;
use App\Models\PaketUmrah;
use App\Models\Agent;

class ManualJamaahController extends Controller
{
    protected int $agentId;

    public function __construct()
    {
        // 🔐 Pastikan user adalah SALES + punya agent
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            abort_unless($user && $user->role === 'SALES', 403);

            $agentId = Agent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('is_active', 1)
                ->value('id');

            abort_unless($agentId, 403);

            $this->agentId = (int) $agentId;

            return $next($request);
        });
    }

    /* =====================================================
     | FORM INPUT JAMAAH MANUAL (LEAD)
     ===================================================== */
    public function create()
    {
        return view('agent.jamaah.manual.create', [
            'paket' => PaketUmrah::where('is_active', 1)
                ->where('status', 'Aktif')
                ->orderBy('tglberangkat')
                ->get(),
        ]);
    }

    /* =====================================================
     | SIMPAN JAMAAH MANUAL
     ===================================================== */
    public function store(
        Request $request,
        ManualJamaahService $service
    ) {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'id_paket'     => 'required|exists:paket_umrah,id',
            'tipe_jamaah'  => 'required|in:reguler,tabungan,cicilan',
        ]);

        $jamaah = $service->create(
            $validated,
            auth()->id()
        );

        return redirect()
            ->route('agent.jamaah.index')
            ->with(
                'success',
                "Jamaah {$jamaah->nama_lengkap} berhasil ditambahkan ke pipeline."
            );
    }
}
