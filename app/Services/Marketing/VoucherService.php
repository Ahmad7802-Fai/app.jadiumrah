<?php

namespace App\Services\Marketing;

use App\Models\Voucher;
use App\Models\Booking;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    public function apply(string $code, Booking $booking): void
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher || !$voucher->isValid()) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak valid.'
            ]);
        }

        $discount = $voucher->calculateDiscount($booking->total_amount);

        $booking->update([
            'voucher_id'       => $voucher->id,
            'voucher_discount' => $discount,
            'total_amount'     => $booking->total_amount - $discount
        ]);

        $voucher->increment('used');
    }
}