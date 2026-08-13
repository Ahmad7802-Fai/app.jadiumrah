<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadClosing;
use Illuminate\Http\Request;

class LeadClosingController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $user = auth()->user();

        abort_unless($user && $user->agent, 403);
        abort_unless($lead->agent_id === $user->agent->id, 403);
        abort_if($lead->status !== 'ACTIVE', 403, 'Lead belum siap closing');

        $data = $request->validate([
            'total_paket' => 'required|numeric|min:1',
            'nominal_dp'  => 'nullable|numeric|min:0',
            'catatan'     => 'nullable|string',
        ]);

        // ❗ 1 LEAD = 1 CLOSING
        if ($lead->closing) {
            return back()->with('error', 'Closing sudah diajukan.');
        }

        LeadClosing::create([
            'lead_id'     => $lead->id,
            'agent_id'    => $user->agent->id,
            'branch_id'   => $user->branch_id,
            'total_paket' => $data['total_paket'],
            'nominal_dp'  => $data['nominal_dp'] ?? 0,
            'catatan'     => $data['catatan'],
            'status'      => 'DRAFT',
        ]);

        // 🔥 KUNCI STATUS KE CLOSING
        $lead->update([
            'status' => 'CLOSING'
        ]);

        return back()->with('success', 'Closing berhasil diajukan.');
    }
}


// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\LeadClosing;
// use Illuminate\Http\Request;

// class LeadClosingController extends Controller
// {
//     public function store(Request $request, Lead $lead)
//     {
//         $user = auth()->user();
//         abort_unless($user && $user->agent, 403);

//         // ❌ tidak boleh double closing
//         abort_if($lead->closing, 403, 'Closing sudah diajukan.');

//         $data = $request->validate([
//             'nominal_dp'  => 'nullable|numeric|min:0',
//             'total_paket' => 'required|numeric|min:0',
//             'catatan'     => 'nullable|string',
//         ]);

//         LeadClosing::create([
//             'lead_id'     => $lead->id,
//             'agent_id'    => $user->agent->id,
//             'branch_id'   => $user->agent->branch_id,
//             'nominal_dp'  => $data['nominal_dp'] ?? null,
//             'total_paket' => $data['total_paket'],
//             'catatan'     => $data['catatan'],
//             'status'      => 'DRAFT',
//         ]);

//         return back()->with('success', 'Closing berhasil diajukan.');
//     }
// }

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Services\Lead\LeadClosingService;
// use Illuminate\Http\Request;

// class LeadClosingController extends Controller
// {
//     public function submit(
//         Lead $lead,
//         LeadClosingService $service
//     ) {
//         // 🔐 pastikan agent
//         abort_unless(auth()->user()->isAgent(), 403);

//         // 🔥 delegasi ke service
//         $service->submit($lead);

//         return redirect()
//             ->route('agent.leads.index')
//             ->with('success', 'Closing berhasil diajukan. Menunggu approval pusat.');
//     }
// }
