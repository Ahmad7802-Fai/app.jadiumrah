<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingWorkflowService
{
    /*
    |--------------------------------------------------------------------------
    | CONFIRM BOOKING
    |--------------------------------------------------------------------------
    */
    public function confirm(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $effectiveStatus = $this->resolveEffectiveStatus($booking);

            if ($effectiveStatus === 'expired') {
                throw new \Exception('Booking sudah kedaluwarsa.');
            }

            if ($booking->status === 'cancelled') {
                throw new \Exception('Booking sudah dibatalkan.');
            }

            // idempotent
            if ($booking->status === 'confirmed') {
                return $booking;
            }

            if (!$this->isFullyPaid($booking)) {
                throw new \Exception('Booking belum lunas.');
            }

            $booking->update([
                'status' => 'confirmed',
            ]);

            return $booking->fresh([
                'paket',
                'departure',
                'jamaahs',
                'payments',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL BOOKING (SAFE + SYNC QUOTA)
    |--------------------------------------------------------------------------
    */
    public function cancel(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {

            $booking = Booking::query()
                ->lockForUpdate()
                ->with('departure')
                ->findOrFail($booking->id);

            // idempotent
            if ($booking->status === 'cancelled') {
                return $booking;
            }

            if ($booking->status === 'confirmed') {
                throw new \Exception('Booking confirmed tidak bisa dibatalkan.');
            }

            $booking->update([
                'status' => 'cancelled',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 🔥 RECALCULATE QUOTA (ANTI DRIFT)
            |--------------------------------------------------------------------------
            */
            $this->syncDepartureQuota($booking);

            return $booking->fresh([
                'paket',
                'departure',
                'jamaahs',
                'payments',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO EXPIRE (CRITICAL)
    |--------------------------------------------------------------------------
    */
    public function expire(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {

            $booking = Booking::query()
                ->lockForUpdate()
                ->with('departure')
                ->findOrFail($booking->id);

            // 🔥 idempotent
            if ($booking->status === 'expired') {
                return $booking;
            }

            if (
                $booking->status !== 'waiting_payment' &&
                $booking->status !== 'partial_paid'
            ) {
                return $booking;
            }

            if (!$booking->expired_at || now()->lte($booking->expired_at)) {
                return $booking;
            }

            /*
            |--------------------------------------------------------------------------
            | SET EXPIRED
            |--------------------------------------------------------------------------
            */
            $booking->update([
                'status' => 'expired',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 🔥 RECALCULATE QUOTA (ANTI DRIFT)
            |--------------------------------------------------------------------------
            */
            $this->syncDepartureQuota($booking);

            return $booking;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 CENTRAL QUOTA SYNC (VERY IMPORTANT)
    |--------------------------------------------------------------------------
    */
    protected function syncDepartureQuota(Booking $booking): void
    {
        if (!$booking->departure) return;

        $departure = $booking->departure;

        $totalBooked = Booking::query()
            ->where('paket_departure_id', $departure->id)
            ->whereNotIn('status', ['expired', 'cancelled'])
            ->sum('qty');

        $departure->update([
            'booked' => $totalBooked,
            'is_closed' => $totalBooked >= $departure->quota,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function resolveEffectiveStatus(Booking $booking): string
    {
        if (
            in_array($booking->status, ['waiting_payment', 'partial_paid'], true) &&
            $booking->expired_at &&
            now()->greaterThan($booking->expired_at)
        ) {
            return 'expired';
        }

        return $booking->status;
    }

    protected function isFullyPaid(Booking $booking): bool
    {
        return $this->resolvePaidAmount($booking) >= (float) $booking->total_amount;
    }

    protected function resolvePaidAmount(Booking $booking): float
    {
        if ($booking->relationLoaded('payments')) {
            return (float) $booking->payments
                ->whereIn('payment_status', ['paid'])
                ->sum('amount');
        }

        return (float) $booking->payments()
            ->where(function ($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere('status', 'paid');
            })
            ->sum('amount');
    }
}