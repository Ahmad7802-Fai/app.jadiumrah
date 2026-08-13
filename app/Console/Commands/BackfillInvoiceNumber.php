<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\Finance\InvoiceService;

class BackfillInvoiceNumber extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'booking:backfill-invoice 
                            {--force : Run without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Generate invoice_number for confirmed bookings that do not have one';

    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        parent::__construct();
        $this->invoiceService = $invoiceService;
    }

    public function handle(): int
    {
        $query = Booking::where('status', 'confirmed')
            ->whereNull('invoice_number');

        $count = $query->count();

        if ($count === 0) {
            $this->info('✅ Tidak ada booking yang perlu dibackfill.');
            return self::SUCCESS;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Ditemukan {$count} booking tanpa invoice. Lanjutkan?")) {
                $this->warn('❌ Dibatalkan.');
                return self::FAILURE;
            }
        }

        $this->info("🚀 Memulai backfill {$count} booking...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $generated = 0;
        $failed    = 0;

        $query->chunk(50, function ($bookings) use (&$generated, &$failed, $bar) {

            foreach ($bookings as $booking) {

                try {
                    $this->invoiceService->generateNumber($booking);
                    $generated++;
                } catch (\Throwable $e) {
                    $failed++;
                    report($e);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Selesai.");
        $this->line("✔ Generated : {$generated}");
        $this->line("✖ Failed    : {$failed}");

        return self::SUCCESS;
    }
}