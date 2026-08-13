<?php

namespace App\Services\Marketing;

use App\Models\FlashSale;
use App\Models\Booking;

class FlashSaleService
{
    public function apply(Booking $booking): void
    {
        $flash = FlashSale::where('paket_id', $booking->paket_id)
            ->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();

        if (!$flash) {
            return;
        }

        if ($flash->seat_limit && $flash->used_seat >= $flash->seat_limit) {
            return;
        }

        $discount = $this->calculateDiscount($flash, $booking->total_amount);

        $booking->update([
            'total_amount' => $booking->total_amount - $discount
        ]);

        $flash->increment('used_seat', $booking->jamaahs()->count());
    }

    private function calculateDiscount(FlashSale $flash, float $amount): float
    {
        if ($flash->discount_type === 'fixed') {
            return min($flash->value, $amount);
        }

        return ($amount * $flash->value) / 100;
    }
}