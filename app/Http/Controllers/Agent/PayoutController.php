<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\Payout\AgentPayoutService;
use App\Models\Agent;
use App\Models\AgentPayoutRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\KomisiLogs;
use RuntimeException;

class PayoutController extends Controller
{
    protected int $agentId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            abort_unless($user && $user->role === 'SALES', 403);

            $agent = Agent::where('user_id', $user->id)->firstOrFail();
            $this->agentId = $agent->id;

            return $next($request);
        });
    }

    /**
     * ==================================================
     * AJUKAN PENCAIRAN KOMISI
     * ==================================================
     */
    public function request(
        AgentPayoutService $service
    ): RedirectResponse {
        try {
            $service->request(
                agentId: $this->agentId,
                userId : auth()->id()
            );

            return redirect()
                ->route('agent.komisi.index')
                ->with('success', 'Pencairan komisi berhasil diajukan.');

        } catch (RuntimeException $e) {
            return redirect()
                ->route('agent.komisi.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * ==================================================
     * RIWAYAT PAYOUT AGENT
     * ==================================================
     */
    public function index()
    {
        $payouts = AgentPayoutRequest::where('agent_id', $this->agentId)
            ->latest()
            ->paginate(10);

        return view('agent.payout.index', compact('payouts'));
    }

public function confirm()
{
    $user  = auth()->user();
    $agent = $user->agent;

    abort_if(! $agent, 403);

    // Ambil komisi yang BENAR-BENAR bisa dicairkan
    $items = KomisiLogs::where('agent_id', $agent->id)
        ->where('status', KomisiLogs::STATUS_AVAILABLE)
        ->whereNull('payout_request_id')
        ->get();

    abort_if($items->isEmpty(), 403, 'Tidak ada komisi yang bisa dicairkan');

return view('agent.payout.confirm', [
    'agent'     => $agent,
    'items'     => $items,
    'total'     => $items->sum('komisi_nominal'),
    'totalItem' => $items->count(),
]);

}

}
