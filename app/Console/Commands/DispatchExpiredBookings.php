<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingLock;
use App\Services\Bookings\BookingWorkflowService;

class DispatchExpiredBookings extends Command
{
    protected $signature = 'bookings:dispatch-expired';

    protected $description = 'Expire bookings & cleanup locks';

    public function handle()
    {
        $this->info('🚀 Start expiring bookings...');

        $service = app(BookingWorkflowService::class);

        $totalChecked = 0;
        $totalExpired = 0;

        /*
        |--------------------------------------------------------------------------
        | EXPIRE BOOKINGS (WITH DEBUG)
        |--------------------------------------------------------------------------
        */
        Booking::query()
            ->whereIn('status', ['waiting_payment', 'partial_paid'])
            ->whereNotNull('expired_at')
            ->orderBy('id')
            ->chunkById(50, function ($bookings) use ($service, &$totalChecked, &$totalExpired) {

                foreach ($bookings as $booking) {

                    $totalChecked++;

                    // DEBUG INFO
                    $this->line(
                        "🔍 Check Booking #{$booking->id} | status={$booking->status} | expired_at={$booking->expired_at}"
                    );

                    // SKIP kalau belum expired
                    if (!$booking->expired_at || now()->lte($booking->expired_at)) {
                        continue;
                    }

                    try {

                        $this->warn("⏳ Expiring Booking #{$booking->id}");

                        $service->expire($booking);

                        $this->info("✅ Expired booking: {$booking->id}");

                        $totalExpired++;

                    } catch (\Throwable $e) {

                        $this->error("❌ ERROR booking {$booking->id}: " . $e->getMessage());

                        \Log::error('BOOKING_EXPIRE_ERROR', [
                            'booking_id' => $booking->id,
                            'message'    => $e->getMessage(),
                        ]);
                    }
                }
            });

        /*
        |--------------------------------------------------------------------------
        | CLEAN LOCK
        |--------------------------------------------------------------------------
        */
        $deletedLocks = BookingLock::query()
            ->where('expired_at', '<', now())
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */
        $this->newLine();
        $this->info("📊 SUMMARY:");
        $this->line("Checked : {$totalChecked}");
        $this->line("Expired : {$totalExpired}");
        $this->line("Locks cleaned : {$deletedLocks}");

        $this->newLine();
        $this->info('✅ Done.');

        return Command::SUCCESS;
    }
}