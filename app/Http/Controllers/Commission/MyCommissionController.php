<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionLog;
use App\Services\Commission\CommissionPayoutService;
use Illuminate\Http\Request;

class MyCommissionController extends Controller
{
    public function __construct(
        protected CommissionPayoutService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | My Commission Logs
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $logs = CommissionLog::where('agent_id', auth()->user()->agent?->id)
            ->whereDoesntHave('payoutItems')
            ->latest()
            ->get();

        return view('commission.my.index', compact('logs'));
    }

    /*
    |--------------------------------------------------------------------------
    | Request Payout
    |--------------------------------------------------------------------------
    */
    public function request(Request $request)
    {
        $validated = $request->validate([
            'log_ids' => 'required|array',
        ]);

        $this->service->request($validated);

        return back()->with('success','Payout request submitted.');
    }
}