<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // ✅ INI YANG BENAR
use App\Models\Lead;
use App\Models\LeadClosing;
use App\Models\Agent;
use App\Services\Lead\LeadClosingService;

class LeadClosingController extends Controller
{
    public function index()
    {
        $closings = LeadClosing::with('lead')
            ->where('status', 'DRAFT')
            ->latest()
            ->paginate(20);

        return view('crm.closing.index', compact('closings'));
    }

    // 👀 detail closing
public function show(LeadClosing $closing)
{
    $closing->load([
        'lead',
        'lead.agent',
        'lead.branch',
    ]);

    $lead = $closing->lead;

    // 🔥 Ambil agent sesuai cabang lead
    $agents = Agent::with('user')
        ->where('branch_id', $lead->branch_id)
        ->orderBy('nama')
        ->get();

    return view('crm.closing.show', compact(
        'closing',
        'lead',
        'agents'
    ));
}

    public function approve(
        Request $request,
        LeadClosing $closing,
        LeadClosingService $service
    ) {
        // 🔒 GUARD UTAMA: TIDAK BOLEH APPROVE ULANG
        abort_if(
            $closing->status === 'APPROVED',
            403,
            'Closing sudah di-approve dan tidak dapat diproses ulang.'
        );

        $data = $request->validate([
            'nominal_dp'  => 'required|numeric|min:0',
            'total_paket' => 'required|numeric|min:0',
            'agent_id'    => 'nullable|exists:agents,id',
            'branch_id'   => 'nullable|exists:branches,id',
            'catatan'     => 'nullable|string|max:1000', // ✅ FIELD BARU
        ]);

        // 🔑 KUNCI UTAMA: SEMUA LOGIC ADA DI SERVICE
        $service->approve($closing->lead, $data);

        return redirect()
            ->route('crm.leads.show', $closing->lead)
            ->with('success', 'Closing disetujui & Jamaah berhasil dibuat.');
    }


    public function submit(Lead $lead, LeadClosingService $service)
    {
        $service->submit($lead);

        return back()->with('success', 'Closing berhasil diajukan.');
    }

    
}


// class LeadClosingController extends Controller
// {
//     /* ================================
//      | LIST APPROVAL (PUSAT)
//      ================================= */
//     public function index()
//     {
//         abort_unless(auth()->user()->isPusat(), 403);

//         $closings = LeadClosing::with([
//                 'lead',
//                 'agent',
//                 'branch'
//             ])
//             ->where('status', 'PENDING')
//             ->latest()
//             ->paginate(20);

//         return view('crm.lead_closings.index', compact('closings'));
//     }
//     public function store(Lead $lead)
//     {
//         abort_if(!$lead->canSubmitClosing(), 403, 'Lead tidak bisa diajukan closing.');

//         $closing = $lead->closings()->create([
//             'status'      => 'DRAFT',
//             'created_by'  => auth()->id(),
//             'branch_id'   => auth()->user()->branch_id,
//         ]);

//         return redirect()
//             ->route('crm.leads.show', $lead)
//             ->with('success', 'Closing berhasil diajukan dan menunggu approval pusat.');
//     }

//     /* ================================
//      | APPROVE
//      ================================= */
//     public function approve(LeadClosing $closing)
//     {
//         abort_unless(auth()->user()->isPusat(), 403);

//         DB::transaction(function () use ($closing) {

//             // update closing
//             $closing->update([
//                 'status'     => 'APPROVED',
//                 'approved_by'=> auth()->id(),
//                 'approved_at'=> now(),
//             ]);

//             // update lead
//             $closing->lead->update([
//                 'status'    => 'CLOSED',
//                 'closed_at'=> now(),
//             ]);
//         });

//         return back()->with('success', 'Closing berhasil disetujui.');
//     }

//     /* ================================
//      | REJECT
//      ================================= */
//     public function reject(Request $request, LeadClosing $closing)
//     {
//         abort_unless(auth()->user()->isPusat(), 403);

//         $request->validate([
//             'reason' => 'required|string|max:1000',
//         ]);

//         DB::transaction(function () use ($closing, $request) {

//             // reject closing
//             $closing->update([
//                 'status'        => 'REJECTED',
//                 'reject_reason' => $request->reason,
//                 'rejected_by'   => auth()->id(),
//                 'rejected_at'   => now(),
//             ]);

//             // rollback lead
//             $closing->lead->update([
//                 'status' => 'WON',
//             ]);
//         });

//         return back()->with('success', 'Closing ditolak.');
//     }

//     public function submit(Lead $lead)
//     {
//         abort_if(
//             $lead->pipeline?->tahap !== 'komit',
//             403,
//             'Lead belum siap closing'
//         );

//         $lead->closing()->create([
//             'status'     => 'PENDING',
//             'submitted_by' => auth()->id(),
//             'submitted_at' => now(),
//         ]);

//         return back()->with('success', 'Closing diajukan.');
//     }
//     public function submit(Lead $lead)
//     {
//         abort_if(
//             $lead->pipeline?->tahap !== 'komit',
//             403,
//             'Lead belum siap closing'
//         );

//         if ($lead->closing) {
//             return back()->with('warning', 'Closing sudah diajukan.');
//         }

//         $lead->closing()->create([
//             'status'        => 'PENDING',
//             'submitted_by'  => auth()->id(),
//             'submitted_at'  => now(),
//         ]);

//         return back()->with('success', 'Closing berhasil diajukan.');
//     }
// }


// namespace App\Http\Controllers\Crm;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\LeadClosing;
// use App\Models\Pipeline;
// use App\Services\Pipeline\PipelineService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class LeadClosingController extends Controller
// {
//     /* =====================================================
//      | INDEX — DAFTAR CLOSING (DRAFT / APPROVED / REJECTED)
//      ===================================================== */
//     public function index(Request $request)
//     {
//         $closings = LeadClosing::with([
//                 'lead.pipeline',
//                 'agent',
//                 'branch',
//             ])
//             ->when($request->q, function ($q) use ($request) {
//                 $q->whereHas('lead', function ($l) use ($request) {
//                     $l->where('nama', 'like', "%{$request->q}%")
//                       ->orWhere('no_hp', 'like', "%{$request->q}%");
//                 });
//             })
//             ->when($request->status, fn ($q) =>
//                 $q->where('status', $request->status)
//             )
//             ->latest()
//             ->paginate(15);

//         return view('crm.closing.index', compact('closings'));
//     }
    
//     public function store(Request $request, Lead $lead)
//     {
//         $data = $request->validate([
//             'nominal_dp' => 'required|numeric|min:0',
//             'closed_at'  => 'required|date',
//             'catatan'    => 'nullable|string',
//         ]);

//         // Cegah double closing
//         if ($lead->closing) {
//             return back()->with('error', 'Lead ini sudah closing.');
//         }

//         LeadClosing::create([
//             'lead_id'    => $lead->id,
//             'agent_id'   => Auth::user()->agent_id ?? null,
//             'branch_id'  => Auth::user()->branch_id ?? null,
//             'nominal_dp' => $data['nominal_dp'],
//             'closed_at'  => $data['closed_at'],
//             'catatan'    => $data['catatan'],
//             'status'     => 'CLOSED',
//         ]);

//         return redirect()
//             ->route('crm.leads.show', $lead)
//             ->with('success', 'Lead berhasil di-closing.');
//     }

//     /* =====================================================
//      | SHOW — DETAIL CLOSING
//      ===================================================== */
//     public function show(LeadClosing $closing)
//     {
//         $closing->load([
//             'lead.pipeline',
//             'agent',
//             'branch'
//         ]);

//         return view('crm.closing.show', compact('closing'));
//     }

//     /* =====================================================
//      | APPROVE — FINAL CLOSE
//      ===================================================== */
//     /* =====================================================
//      | APPROVE CLOSING — FINAL
//      ===================================================== */
//     public function approve(
//         LeadClosing $closing,
//         PipelineService $pipelineService
//     ) {
//         $this->authorize('approve', $closing);

//         DB::transaction(function () use ($closing, $pipelineService) {

//             $closing->update([
//                 'status'       => 'APPROVED',
//                 'approved_by'  => auth()->id(),
//                 'approved_at'  => now(),
//             ]);

//             $lead = $closing->lead;

//             $pipelineService->transition(
//                 $lead,
//                 Pipeline::where('tahap', 'komit')->firstOrFail(),
//                 'APPROVE_CLOSING',
//                 auth()->user() // ✅ USER OBJECT
//             );

//             $lead->update([
//                 'status' => 'CLOSED'
//             ]);
//         });

//         return back()->with('success', 'Closing berhasil disetujui.');
//     }

//     /* =====================================================
//      | REJECT — TOLAK CLOSING
//      ===================================================== */
//     public function reject(
//         LeadClosing $closing,
//         PipelineService $pipelineService
//     ) {
//         $this->authorize('approve', $closing);

//         DB::transaction(function () use ($closing, $pipelineService) {

//             $closing->update([
//                 'status'      => 'REJECTED',
//                 'rejected_by'=> auth()->id(),
//                 'rejected_at'=> now(),
//             ]);

//             $lead = $closing->lead;

//             // ⬅️ KEMBALI KE FOLLOWUP
//             $pipelineService->transition(
//                 $lead,
//                 Pipeline::where('tahap', 'followup')->firstOrFail(),
//                 'REJECT_CLOSING',
//                 auth()->user()
//             );

//             // 🔓 UNLOCK
//             $lead->update([
//                 'status' => 'ACTIVE'
//             ]);
//         });

//         return back()->with('success', 'Closing ditolak, lead dikembalikan ke follow up.');
//     }

// }
