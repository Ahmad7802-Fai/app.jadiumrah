<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketDepartureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $quota = (int) ($this->quota ?? 0);
        $booked = (int) ($this->booked ?? 0);
        $quotaRemaining = (int) ($this->quota_remaining ?? 0);

        $isClosed = (bool) $this->is_closed;
        $isAvailable = (bool) $this->is_available;

        /*
        |--------------------------------------------------------------------------
        | 🔥 HITUNG FINAL PRICE (CORE FIX)
        |--------------------------------------------------------------------------
        */
        $prices = $this->whenLoaded('prices');

        $finalPrices = collect($prices)->map(function ($p) {
            return $p->final_price ?? $p->price ?? null;
        })->filter();

        $basePrice = $finalPrices->min();
        $originalPrice = collect($prices)->pluck('price')->filter()->min();

        return [
            'id' => $this->id,
            'departure_code' => $this->departure_code,
            'flight_number' => $this->flight_number,
            'meeting_point' => $this->meeting_point,

            'departure_date' => optional($this->departure_date)?->toJSON(),
            'return_date' => optional($this->return_date)?->toJSON(),

            'quota' => $quota,
            'booked' => $booked,
            'quota_remaining' => $quotaRemaining,
            'quota_label' => $this->formatQuotaLabel($quotaRemaining),

            'occupancy_percentage' => $this->occupancy_percentage !== null
                ? (float) $this->occupancy_percentage
                : null,

            'is_active' => (bool) $this->is_active,
            'is_closed' => $isClosed,
            'is_available' => $isAvailable,
            'availability_label' => $this->formatAvailabilityLabel($isClosed, $isAvailable, $quotaRemaining),

            /*
            |--------------------------------------------------------------------------
            | 🔥 PRICE FINAL
            |--------------------------------------------------------------------------
            */
            'price_start_from' => $basePrice,
            'price_label' => $this->formatPrice($basePrice),

            'original_price' => $originalPrice,
            'original_price_label' => $this->formatPrice($originalPrice),

            'has_discount' => $originalPrice > $basePrice,

            'discount_percent' => $this->calculateDiscount($originalPrice, $basePrice),

            'prices' => PaketDeparturePriceResource::collection($prices),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function formatPrice($price): ?string
    {
        if ($price === null || $price <= 0) return null;

        return 'Rp' . number_format((float) $price, 0, ',', '.');
    }

    protected function formatQuotaLabel(int $quotaRemaining): string
    {
        return $quotaRemaining > 0
            ? 'Sisa ' . $quotaRemaining . ' seat'
            : 'Seat habis';
    }

    protected function formatAvailabilityLabel(bool $isClosed, bool $isAvailable, int $quotaRemaining): string
    {
        if ($isClosed) return 'Ditutup';
        if ($isAvailable && $quotaRemaining > 0) return 'Tersedia';

        return 'Penuh';
    }

    protected function calculateDiscount($original, $final): int
    {
        if (!$original || !$final) return 0;
        if ($original <= $final) return 0;

        return (int) round((($original - $final) / $original) * 100);
    }
}