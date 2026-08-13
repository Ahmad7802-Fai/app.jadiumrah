<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\Finance\PaymentService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use Barryvdh\DomPDF\Facade\Pdf;
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, Booking $booking)
    {
        $this->authorize('create', Payment::class);

        $request->validate([
            'method' => 'required|in:transfer,cash,gateway,edc',
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:100',
            'fee_amount' => 'nullable|numeric|min:0',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'note' => 'nullable|string|max:500',
        ]);

        try {

            $filePath = null;

            if ($request->hasFile('proof_file')) {
                $filePath = $request->file('proof_file')
                    ->store('payments/proofs','public');
            }

            $payment = $this->paymentService->create([
                'booking_id' => $booking->id,
                'method' => $request->method,
                'amount' => $request->amount,
                'reference_number' => $request->reference_number,
                'fee_amount' => $request->fee_amount ?? 0,
                'channel' => 'website',
                'proof_file' => $filePath,
                'note' => $request->note,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment created successfully',
                'data' => new PaymentResource($payment)
            ],201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],400);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function approve(Payment $payment)
    {
        $this->authorize('approve',$payment);

        try {

            $payment = $this->paymentService->approve($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment approved',
                'data' => new PaymentResource($payment)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],400);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT PAYMENT
    |--------------------------------------------------------------------------
    */

    public function reject(Payment $payment)
    {
        $this->authorize('approve',$payment);

        try {

            $payment = $this->paymentService->reject($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected',
                'data' => new PaymentResource($payment)
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],400);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PAYMENT
    |--------------------------------------------------------------------------
    */

    public function destroy(Payment $payment)
    {
        $this->authorize('delete',$payment);

        $this->paymentService->delete($payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LIST PAYMENT BY BOOKING
    |--------------------------------------------------------------------------
    */

    public function byBooking(Booking $booking)
    {
        $this->authorize('view',$booking);

        $payments = $booking->payments()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments)
        ]);
    }

    public function receipt(Payment $payment)
    {
        if ($payment->status !== 'paid') {
            abort(403,'Receipt hanya untuk pembayaran yang sudah diterima.');
        }

        $payment->load([
            'booking',
            'branch',
            'jamaah'
        ]);

        $pdf = Pdf::loadView(
            'finance.payments.receipt_pdf',
            compact('payment')
        );

        return $pdf->stream(
            $payment->receipt_number.'.pdf'
        );
    }
}