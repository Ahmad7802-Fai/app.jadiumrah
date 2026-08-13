<?php

namespace App\Services\Finance;

use App\Models\Booking;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    protected CodeGeneratorService $codeService;

    public function __construct(CodeGeneratorService $codeService)
    {
        $this->codeService = $codeService;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE INVOICE NUMBER (SAFE)
    |--------------------------------------------------------------------------
    */
    public function generateNumber(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {

            // 🔒 Lock booking row
            $booking = Booking::lockForUpdate()->find($booking->id);

            // Kalau sudah ada invoice_number → return saja
            if ($booking->invoice_number) {
                return $booking;
            }

            $invoiceNumber = $this->codeService->generate(
                prefix: 'INV',
                entity: 'invoice',
                pad: 5,
                yearly: true
            );

            $booking->invoice_number = $invoiceNumber;
            $booking->save();

            return $booking;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD INVOICE DATA
    |--------------------------------------------------------------------------
    */
    public function build(Booking $booking): array
    {
        // Pastikan invoice number ada
        $booking = $this->generateNumber($booking);

        $booking->load([
            'jamaahs',
            'paket',
            'departure',
            'branch',
            'agent',
        ]);

        $paid = $booking->payments()
            ->where('status', 'paid')
            ->sum('amount');

        $remaining = max(
            0,
            $booking->total_amount - $paid
        );

        return [
            'booking'   => $booking,
            'payments'  => $booking->payments()
                                   ->where('status','paid')
                                   ->get(),
            'paid'      => $paid,
            'remaining' => $remaining,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VOID INVOICE (OPTIONAL FUTURE FEATURE)
    |--------------------------------------------------------------------------
    */
    public function resetNumber(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {

            $booking = Booking::lockForUpdate()->find($booking->id);

            $booking->invoice_number = null;
            $booking->save();
        });
    }
}