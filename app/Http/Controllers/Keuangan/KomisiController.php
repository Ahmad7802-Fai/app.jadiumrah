<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Services\Komisi\KomisiApprovalService;
use App\Models\KomisiLogs;
use Illuminate\Http\RedirectResponse;
use App\Models\Agent;
use RuntimeException;
use Illuminate\Http\Request;
class KomisiController extends Controller
{
    /* ============================================================
     | INDEX — LIST KOMISI PENDING
     ============================================================ */
    public function index(Request $request)
    {
        $query = KomisiLogs::with(['agent.user', 'jamaah'])
            ->where('status', KomisiLogs::STATUS_PENDING);

        // ===============================
        // FILTER AGENT
        // ===============================
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // ===============================
        // FILTER MODE
        // ===============================
        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        // ===============================
        // FILTER TANGGAL
        // ===============================
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $komisi = $query
            ->orderBy('created_at', 'asc')
            ->paginate(20)
            ->withQueryString();

        // 🔑 INI YANG KURANG
        $agents = Agent::with('user')
            ->orderBy('kode_agent')
            ->get();

        return view('keuangan.komisi.index', compact(
            'komisi',
            'agents'
        ));
    }

    /* ============================================================
     | APPROVE KOMISI
     | pending → available
     ============================================================ */
    public function approve(
        int $id,
        KomisiApprovalService $service
    ): RedirectResponse {
        try {
            $service->approve(
                komisiId: $id,
            );

            return back()->with(
                'success',
                'Komisi berhasil disetujui dan siap dicairkan.'
            );

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /* ============================================================
     | REJECT KOMISI
     | pending → rejected
     ============================================================ */
    public function reject(
        int $id,
        KomisiApprovalService $service
    ): RedirectResponse {
        try {
            $service->reject(
                komisiId: $id,
                adminId : auth()->id(),
                reason  : request('reason')
            );

            return back()->with(
                'success',
                'Komisi berhasil ditolak.'
            );

        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function show(int $id)
    {
        $komisi = KomisiLogs::with([
            'agent.user',
            'jamaah',
            'payment.invoice',
            'payout',
        ])->findOrFail($id);

        return view('keuangan.komisi.show', compact('komisi'));
    }

}


// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use App\Services\Komisi\KomisiApprovalService;
// use App\Models\KomisiLogs;
// use Illuminate\Http\RedirectResponse;
// use RuntimeException;

// class KomisiController extends Controller
// {
//     /* ============================================================
//      | INDEX — LIST KOMISI PENDING
//      ============================================================ */
//     public function index()
//     {
//         $komisi = KomisiLogs::with(['agent', 'jamaah'])
//             ->where('status', KomisiLogs::STATUS_PENDING)
//             ->orderBy('created_at', 'asc')
//             ->paginate(20);

//         return view('keuangan.komisi.index', compact('komisi'));
//     }

//     /* ============================================================
//      | SET KOMISI AVAILABLE (KEUANGAN)
//      ============================================================ */
//     public function approve(
//         int $id,
//         KomisiApprovalService $service
//     ): RedirectResponse {
//         try {
//             $service->makeAvailable(
//                 komisiId: $id,
//                 adminId : auth()->id()
//             );

//             return back()->with('success', 'Komisi berhasil disetujui & siap dicairkan.');

//         } catch (RuntimeException $e) {

//             return back()->with('error', $e->getMessage());
//         }
//     }
// }

// namespace App\Http\Controllers\Keuangan;

// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
// use App\Models\KomisiLogs;

// class KomisiController extends Controller
// {
//     /* ============================================================
//      | INDEX — LIST KOMISI PENDING
//      ============================================================ */
//     public function index()
//     {
//         $komisi = KomisiLogs::with(['agent', 'jamaah'])
//             ->where('status', 'pending')
//             ->orderBy('created_at', 'asc')
//             ->paginate(20);

//         return view('keuangan.komisi.index', compact('komisi'));
//     }

//     /* ============================================================
//      | APPROVE KOMISI
//      ============================================================ */
//     public function approve(int $id)
//     {
//         DB::transaction(function () use ($id) {

//             $komisi = KomisiLogs::lockForUpdate()
//                 ->findOrFail($id);

//             if ($komisi->status !== 'pending') {
//                 throw new \Exception('Komisi sudah diproses.');
//             }

//             $komisi->update([
//                 'status' => 'approved',
//             ]);
//         });

//         return back()->with('success', 'Komisi berhasil disetujui.');
//     }
// }
