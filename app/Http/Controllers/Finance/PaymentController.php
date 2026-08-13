<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Services\Finance\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $user = auth()->user();

        $payments = Payment::with([
            'booking',
            'booking.paket',
            'jamaah',
            'branch',
            'creator',
            'approver'
        ])
        ->visibleFor($user)

        ->when($request->search, function ($query, $search) {

            $query->where(function ($q) use ($search) {

                $q->where('payment_code', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });

        })

        ->when($request->status, function ($query, $status) {

            $query->where('status', $status);

        })

        ->latest()
        ->paginate(15)
        ->withQueryString();

        return view('finance.payments.index', compact('payments'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(Booking $booking)
    {
        $this->authorize('create', Payment::class);
        $this->authorize('view', $booking);

        return view('finance.payments.create', compact('booking'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->authorize('create', Payment::class);

        $validated = $request->validate([

            'booking_id' => 'required|exists:bookings,id',

            'jamaah_id' => 'nullable|exists:jamaahs,id',

            'method' => 'required|in:' . implode(',', Payment::METHODS),

            'amount' => 'required|numeric|min:1',

            'paid_at' => 'required|date',

            'note' => 'nullable|string|max:255',

            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        $this->authorize('view', $booking);

        /*
        |--------------------------------------------------------------------------
        | FILE UPLOAD
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('proof_file')) {

            $validated['proof_file'] = $request
                ->file('proof_file')
                ->store('payments/proofs', 'public');
        }

        try {

            $this->service->create($validated);

            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'Payment berhasil dibuat dan menunggu approval.');

        } catch (\Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Payment $payment)
    {
        $this->authorize('update', $payment);

        return view('finance.payments.edit', compact('payment'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('update', $payment);

        $validated = $request->validate([

            'method' => 'required|in:' . implode(',', Payment::METHODS),

            'amount' => 'required|numeric|min:1',

            'note' => 'nullable|string|max:255',

            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILE REPLACE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('proof_file')) {

            if (
                $payment->proof_file &&
                Storage::disk('public')->exists($payment->proof_file)
            ) {
                Storage::disk('public')->delete($payment->proof_file);
            }

            $validated['proof_file'] = $request
                ->file('proof_file')
                ->store('payments/proofs', 'public');
        }

        try {

            $this->service->update($payment, $validated);

            return redirect()
                ->route('finance.payments.index')
                ->with('success', 'Payment berhasil diperbarui.');

        } catch (\Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(Payment $payment)
    {
        $this->authorize('approve', $payment);

        try {

            $this->service->approve($payment);

            return back()->with('success', 'Payment berhasil di-approve.');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(Payment $payment)
    {
        $this->authorize('approve', $payment);

        try {

            $this->service->reject($payment);

            return back()->with('success', 'Payment berhasil ditolak.');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Payment $payment)
    {
        $this->authorize('delete', $payment);

        try {

            $this->service->delete($payment);

            return back()->with('success', 'Payment berhasil dihapus.');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIPT PDF
    |--------------------------------------------------------------------------
    */

    public function receipt(Payment $payment)
    {
        $this->authorize('view', $payment);

        if ($payment->status !== 'paid') {
            abort(403, 'Receipt hanya tersedia untuk payment yang sudah dibayar.');
        }

        $payment->load([
            'booking',
            'jamaah',
            'branch'
        ]);

        $pdf = Pdf::loadView(
            'finance.payments.receipt_pdf',
            compact('payment')
        )->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        if (request()->download) {
            return $pdf->download($payment->receipt_number . '.pdf');
        }

        return $pdf->stream($payment->receipt_number . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | INVOICE PDF
    |--------------------------------------------------------------------------
    */

    public function invoice(Booking $booking)
    {
        $this->authorize('viewInvoice', $booking);

        if (!$booking->invoice_number) {
            abort(404, 'Invoice belum tersedia.');
        }

        $booking->load([
            'jamaahs',
            'departure',
            'paket',
            'payments'
        ]);

        $pdf = Pdf::loadView(
            'finance.invoices.invoice_pdf',
            compact('booking')
        )->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);

        if (request()->download) {
            return $pdf->download($booking->invoice_number . '.pdf');
        }

        return $pdf->stream($booking->invoice_number . '.pdf');
    }
}