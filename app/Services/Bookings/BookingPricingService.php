<?php

namespace App\Services\Bookings;

use App\Models\PaketDeparturePrice;
use App\Models\PaketPrice;

class BookingPricingService
{
    /*
    |--------------------------------------------------------------------------
    | Resolve Final Price Per Seat
    |--------------------------------------------------------------------------
    */
    public function resolve(
        int $departureId,
        int $paketId,
        string $roomType
    ): float {

        // 1️⃣ Priority: Departure Specific Price
        $departurePrice = PaketDeparturePrice::where('paket_departure_id', $departureId)
            ->where('room_type', $roomType)
            ->value('price');

        if ($departurePrice) {
            return (float) $departurePrice;
        }

        // 2️⃣ Fallback: Paket Base Price
        $paketPrice = PaketPrice::where('paket_id', $paketId)
            ->where('room_type', $roomType)
            ->value('price');

        if (!$paketPrice) {
            throw new \Exception("Harga untuk room type {$roomType} tidak ditemukan.");
        }

        return (float) $paketPrice;
    }
}