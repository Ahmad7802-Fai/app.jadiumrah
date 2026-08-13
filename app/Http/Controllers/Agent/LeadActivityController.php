<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadActivityController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $user = auth()->user();

        // ===============================
        // SECURITY
        // ===============================
        abort_unless($user && $user->agent, 403);

        abort_unless(
            $lead->agent_id === $user->agent->id,
            403
        );

        // ===============================
        // VALIDATION
        // ===============================
        $data = $request->validate([
            'aktivitas' => 'required|in:wa,telpon,dm,meeting,kunjungan,presentasi,followup,closing',
            'hasil' => 'required|string',
            'next_action' => 'nullable|string',
            'followup_date' => 'nullable|date',
        ]);

        // ===============================
        // STORE ACTIVITY (🔥 OBSERVER JALAN)
        // ===============================
        app(\App\Services\Lead\LeadActivityService::class)
            ->store($lead, $data);

        return back()->with('success', 'Follow up berhasil disimpan.');
    }
}

// namespace App\Http\Controllers\Agent;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Services\Lead\LeadActivityService;
// use Illuminate\Http\Request;
// use Illuminate\Validation\ValidationException;

// class LeadActivityController extends Controller
// {
// public function store(Request $request, Lead $lead)
// {
//     $user = auth()->user();

//     abort_unless($user, 403);
//     abort_unless($user->agent, 403);

//     abort_unless(
//         $lead->agent_id === $user->agent->id,
//         403
//     );

//     $data = $request->validate([
//         'aktivitas'     => 'required|in:wa,telpon,kunjungan,presentasi,dm,meeting',
//         'hasil'         => 'required|string',
//         'next_action'   => 'nullable|string',
//         'followup_date' => 'nullable|date',
//     ]);

//     app(\App\Services\Lead\LeadActivityService::class)
//         ->store($lead, $data);

//     return back()->with('success', 'Follow up berhasil disimpan.');
// }

// }

