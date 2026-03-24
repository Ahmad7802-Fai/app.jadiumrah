<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketDepartureSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $quota = (int) ($this->quota ?? 0);
        $booked = (int) ($this->booked ?? 0);
        $quotaRemaining = (int) ($this->quota_remaining ?? 0);
        $isClosed = (bool) $this->is_closed;
        $isAvailable = (bool) $this->is_available;

        return [
            'id' => $this->id,
            'departure_code' => $this->departure_code,

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

            'price_start_from' => $this->price_start_from,
            'price_label' => $this->formatPrice($this->price_start_from),

            /*
            |--------------------------------------------------------------------------
            | 🔥 FIX: PROMO SOURCE
            |--------------------------------------------------------------------------
            */
            'prices' => PaketDeparturePriceResource::collection(
                $this->whenLoaded('prices')
            ),
        ];
    }

    protected function formatPrice($price): ?string
    {
        if ($price === null) return null;

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
}