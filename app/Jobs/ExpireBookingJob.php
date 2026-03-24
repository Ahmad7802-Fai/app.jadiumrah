<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Events\BookingExpired;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExpireBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $bookingId;

    public function __construct(int $bookingId)
    {
        $this->bookingId = $bookingId;
    }

    public function handle(): void
    {
        DB::transaction(function () {

            // Lock booking row
            $booking = Booking::where('id', $this->bookingId)
                ->where('status', 'draft')
                ->lockForUpdate()
                ->first();

            if (!$booking) {
                return;
            }

            // Not yet expired
            if (!$booking->expired_at || now()->lt($booking->expired_at)) {
                return;
            }

            $departure = $booking->departure()
                ->lockForUpdate()
                ->first();

            if ($departure) {

                $qty = max(1, (int) $booking->qty);

                // Prevent negative booked
                if ($departure->booked >= $qty) {
                    $departure->decrement('booked', $qty);
                }

                // Auto reopen departure
                if ($departure->is_closed && config('booking.auto_reopen_departure')) {
                    $departure->update(['is_closed' => false]);
                }
            }

            // Update booking status
            $booking->update([
                'status' => 'expired'
            ]);

            // Fire domain event
            event(new BookingExpired($booking));
        });
    }
}