<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\TicketRefund;
use App\Services\Ticketing\TicketRefundApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TicketRefundApprovalController extends Controller
{
    public function index()
    {
        $refunds = TicketRefund::with(['invoice.pnr'])
            ->where('approval_status', 'PENDING')
            ->orderBy('refunded_at', 'desc') // ✅ KOLOM REAL
            ->get();

        return view('ticketing.refund.approval', compact('refunds'));
    }


    public function approve(
    TicketRefund $refund,
    TicketRefundApprovalService $service
    ) {
        $this->authorize('approve', $refund);

        $service->approve($refund, auth()->id());

        return back()->with('success', 'Refund approved.');
    }

    public function reject(
        TicketRefund $refund,
        TicketRefundApprovalService $service
    ) {
        $this->authorize('approve', $refund);

        $service->reject($refund, auth()->id());

        return back()->with('success', 'Refund rejected.');
    }


}
