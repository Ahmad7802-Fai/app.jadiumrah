<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Facades\DB;

class BackfillBookingCode extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:backfill-code 
                            {--dry-run : Only simulate without saving}';

    /**
     * The console command description.
     */
    protected $description = 'Generate booking_code for old bookings that are NULL';

    public function handle(CodeGeneratorService $codeService)
    {
        $this->info('Starting booking_code backfill...');

        $dryRun = $this->option('dry-run');

        $bookings = Booking::whereNull('booking_code')
            ->orderBy('id')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings need backfill.');
            return Command::SUCCESS;
        }

        $this->info("Found {$bookings->count()} bookings without booking_code.");

        DB::transaction(function () use ($bookings, $codeService, $dryRun) {

            foreach ($bookings as $booking) {

                $code = $codeService->generate(
                    prefix: 'BOOK',
                    entity: 'booking',
                    pad: 5,
                    yearly: true
                );

                if (!$dryRun) {
                    $booking->update([
                        'booking_code' => $code
                    ]);
                }

                $this->line("✔ Booking ID {$booking->id} → {$code}");
            }
        });

        if ($dryRun) {
            $this->warn('Dry run mode ON — no data saved.');
        } else {
            $this->info('Backfill completed successfully.');
        }

        return Command::SUCCESS;
    }
}