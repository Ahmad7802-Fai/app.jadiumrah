<?php 

namespace App\Services\Finance;

use App\Models\Refund;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use App\Services\CodeGeneratorService;

class RefundService
{
    public function __construct(
        protected CodeGeneratorService $codeService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE REFUND
    |--------------------------------------------------------------------------
    */
    public function create(array $data): Refund
    {
        return DB::transaction(function () use ($data) {

            $payment = Payment::with('refunds')
                ->lockForUpdate()
                ->findOrFail($data['payment_id']);

            $user = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            if ($payment->status !== 'paid') {
                throw new \Exception('Refund hanya bisa untuk payment approved.');
            }

            $totalRefunded = $payment->refunds()
                ->where('status', 'approved')
                ->sum('amount');

            $maxRefund = $payment->amount - $totalRefunded;

            if ($data['amount'] > $maxRefund) {
                throw new \Exception(
                    'Jumlah refund melebihi sisa yang bisa direfund.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE CODE
            |--------------------------------------------------------------------------
            */

            $code = $this->codeService->generate(
                prefix: 'RFND',
                entity: 'refund',
                pad: 5,
                yearly: true
            );

            return Refund::create([
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
                'branch_id'  => $user->branch_id,
                'refund_code'=> $code,
                'amount'     => $data['amount'],
                'reason'     => $data['reason'] ?? null,
                'status'     => 'pending',
                'created_by' => $user->id,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE REFUND
    |--------------------------------------------------------------------------
    */
    public function approve(Refund $refund): Refund
    {
        return DB::transaction(function () use ($refund) {

            $refund->lockForUpdate();

            if ($refund->status !== 'pending') {
                return $refund;
            }

            $payment = $refund->payment;
            $booking = $refund->booking;

            if ($payment->status !== 'paid') {
                throw new \Exception('Refund tidak valid. Payment belum approved.');
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE RECEIPT NUMBER
            |--------------------------------------------------------------------------
            */

            $receiptNumber = $this->codeService->generate(
                prefix: 'RFD',
                entity: 'refund_receipt',
                pad: 5,
                yearly: true
            );

            $refund->update([
                'status'                => 'approved',
                'approved_by'           => auth()->id(),
                'approved_at'           => now(),
                'refund_receipt_number' => $receiptNumber,
            ]);

            $this->syncBookingAfterRefund($booking);

            return $refund;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT REFUND
    |--------------------------------------------------------------------------
    */
    public function reject(Refund $refund): Refund
    {
        if ($refund->status !== 'pending') {
            return $refund;
        }

        $refund->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $refund;
    }

    /*
    |--------------------------------------------------------------------------
    | SYNC BOOKING AFTER REFUND
    |--------------------------------------------------------------------------
    */
    protected function syncBookingAfterRefund(Booking $booking): void
    {
        $totalPaid = $booking->payments()
            ->where('status', 'paid')
            ->sum('amount');

        $totalRefund = $booking->refunds()
            ->where('status', 'approved')
            ->sum('amount');

        $finalPaid = max(0, $totalPaid - $totalRefund);

        $booking->update([
            'paid_amount' => $finalPaid,
            'payment_status' => match (true) {
                $finalPaid >= $booking->total_amount => 'lunas',
                $finalPaid > 0                       => 'partial',
                default                              => 'belum_lunas',
            }
        ]);
    }
}