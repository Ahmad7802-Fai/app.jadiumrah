<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\Commission\CommissionCalculatorService;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        if ($booking->status === 'confirmed') {
            app(CommissionCalculatorService::class)
                ->calculate($booking);
        }
    }

    public function updated(Booking $booking): void
    {
        if (
            $booking->wasChanged('status') &&
            $booking->status === 'confirmed'
        ) {
            app(CommissionCalculatorService::class)
                ->calculate($booking);
        }
    }
}