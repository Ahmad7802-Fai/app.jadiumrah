<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionPayout;
use App\Services\Commission\CommissionPayoutService;
use Illuminate\Http\Request;

class CommissionPayoutController extends Controller
{
    public function __construct(
        protected CommissionPayoutService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX (Finance)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $payouts = CommissionPayout::with(['agent','branch'])
            ->latest()
            ->paginate(15);

        return view('commission.payouts.index', compact('payouts'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE (Finance)
    |--------------------------------------------------------------------------
    */
    public function approve(CommissionPayout $payout)
    {
        $this->authorize('approve', $payout);

        $this->service->approve($payout);

        return back()->with('success','Payout approved.');
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID (Finance)
    |--------------------------------------------------------------------------
    */
    public function markAsPaid(CommissionPayout $payout)
    {
        $this->authorize('pay', $payout);

        $this->service->markAsPaid($payout);

        return back()->with('success','Payout marked as paid.');
    }
}