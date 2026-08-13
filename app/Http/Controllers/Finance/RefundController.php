<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Payment;
use App\Services\Finance\RefundService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(
        protected RefundService $service
    ) {}

    public function index()
    {
        $refunds = Refund::with(['payment','booking','branch'])
            ->latest()
            ->paginate(15);

        return view('finance.refunds.index', compact('refunds'));
    }

    public function create(Payment $payment)
    {
        return view('finance.refunds.create', compact('payment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount'     => 'required|numeric|min:1',
            'reason'     => 'nullable|string',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('finance.refunds.index')
            ->with('success','Refund dibuat dan menunggu approval.');
    }

    public function approve(Refund $refund)
    {
        $this->service->approve($refund);

        return back()->with('success','Refund approved.');
    }

    public function reject(Refund $refund)
    {
        $this->service->reject($refund);

        return back()->with('success','Refund rejected.');
    }

public function receipt(Refund $refund)
{
    if ($refund->status !== 'approved') {
        abort(403, 'Refund belum disetujui.');
    }

    $refund->load([
        'payment.booking.jamaahs',
        'payment.booking.branch',
        'payment.booking.paket',
    ]);

    $pdf = Pdf::loadView('finance.refunds.receipt_pdf', [
        'refund' => $refund
    ])->setPaper('A4');

    return $pdf->stream(
        $refund->refund_receipt_number . '.pdf'
    );
}

}