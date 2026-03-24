<?php

namespace App\Listeners;

use App\Events\BookingExpired;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleBookingExpired implements ShouldQueue
{
    public function handle(BookingExpired $event): void
    {
        $booking = $event->booking;

        /*
        |--------------------------------------------------------------------------
        | 1. Logging
        |--------------------------------------------------------------------------
        */
        \Log::info("Booking expired: {$booking->booking_code}");

        /*
        |--------------------------------------------------------------------------
        | 2. TODO: WhatsApp Notification
        |--------------------------------------------------------------------------
        */
        // dispatch(new SendBookingExpiredWhatsApp($booking));

        /*
        |--------------------------------------------------------------------------
        | 3. TODO: Email Notification
        |--------------------------------------------------------------------------
        */
        // Mail::to($booking->user->email)->send(...);

        /*
        |--------------------------------------------------------------------------
        | 4. TODO: CRM Sync
        |--------------------------------------------------------------------------
        */
        // CRM::pushExpiredBooking($booking);
    }
}