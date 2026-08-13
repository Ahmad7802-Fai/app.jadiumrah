<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\AgentPayoutRequest;
use App\Services\Payout\KeuanganPayoutService;
use App\Services\Payout\PayoutPaymentService;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use RuntimeException;

class PayoutController extends Controller
{
    /* ============================================================
     | INDEX — LIST PAYOUT
     ============================================================ */
    public function index()
    {
        $query = AgentPayoutRequest::with([
            'agent.user',
            'branch',
        ]);

        // ===============================
        // FILTER STATUS
        // ===============================
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // ===============================
        // FILTER AGENT
        // ===============================
        if ($agentId = request('agent_id')) {
            $query->where('agent_id', $agentId);
        }

        // ===============================
        // FILTER BRANCH
        // ===============================
        if ($branchId = request('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        // ===============================
        // FILTER TANGGAL
        // ===============================
        if ($from = request('date_from')) {
            $query->whereDate('requested_at', '>=', $from);
        }

        if ($to = request('date_to')) {
            $query->whereDate('requested_at', '<=', $to);
        }

        $payouts = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $agents   = \App\Models\Agent::orderBy('nama')->get();
        $branches = \App\Models\Branch::orderBy('nama_cabang')->get();

        return view('keuangan.payouts.index', compact(
            'payouts',
            'agents',
            'branches'
        ));
    }

    /* ============================================================
     | SHOW — DETAIL PAYOUT
     ============================================================ */
    public function show(int $id)
    {
        $payout = AgentPayoutRequest::with([
            'agent.user',
            'branch',
            'komisiLogs.jamaah',
            'komisiLogs.payment.invoice',
            'transfer', // 🔑 SNAPSHOT TRANSFER
        ])->findOrFail($id);

        $komisi = $payout->komisiLogs->sortBy('created_at');

        return view('keuangan.payouts.show', compact('payout', 'komisi'));
    }

    /* ============================================================
     | APPROVE PAYOUT
     | requested → approved
     ============================================================ */
    public function approve(
        int $id,
        KeuanganPayoutService $service
    ): RedirectResponse {
        try {
            $service->approve(
                payoutId: $id,
                adminId : auth()->id()
            );

            return back()->with(
                'success',
                'Payout berhasil disetujui.'
            );

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /* ============================================================
     | PAY PAYOUT (FINAL)
     | approved → paid
     | + snapshot rekening
     ============================================================ */
    public function pay(
        int $id,
        PayoutPaymentService $service
    ): RedirectResponse {
        try {
            $service->pay(
                payoutId: $id,
                adminId : auth()->id()
            );

            return back()->with(
                'success',
                'Payout berhasil ditandai sebagai PAID.'
            );

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /* ============================================================
     | REJECT PAYOUT
     | requested → rejected
     | komisi requested → available
     ============================================================ */
    public function reject(
        int $id,
        KeuanganPayoutService $service
    ): RedirectResponse {
        try {
            $service->reject(
                payoutId: $id,
                adminId : auth()->id(),
                reason  : request('reason')
            );

            return back()->with(
                'success',
                'Payout berhasil ditolak.'
            );

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdf(int $id)
    {
        $payout = AgentPayoutRequest::with([
            'agent.user',
            'branch',
            'transfer',
            'komisi.jamaah',
            'komisi.payment.invoice',
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'keuangan.payouts.pdf',
            compact('payout')
        )->setPaper('A4');

        // 👇 INLINE (BUKA DI TAB BARU)
        return $pdf->stream(
            'payout-'.$payout->id.'.pdf',
            ['Attachment' => false]
        );
    }
}

