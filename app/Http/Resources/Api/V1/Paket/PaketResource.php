<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $base = (float) ($this->base_price ?? 0);
        $original = (float) ($this->original_price ?? 0);

        return [

            /*
            |--------------------------------------------------------------------------
            | BASIC
            |--------------------------------------------------------------------------
            */
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'slug' => $this->slug,

            /*
            |--------------------------------------------------------------------------
            | INFO
            |--------------------------------------------------------------------------
            */
            'departure_city' => $this->formatText($this->departure_city),
            'duration_days' => $this->duration_days ? (int) $this->duration_days : null,
            'duration_label' => $this->duration_days ? $this->duration_days . ' Hari' : null,
            'airline' => $this->formatText($this->airline),
            'short_description' => $this->short_description,

            /*
            |--------------------------------------------------------------------------
            | IMAGE (🔥 FIX CDN)
            |--------------------------------------------------------------------------
            */
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $media->url($this->thumbnail),

            /*
            |--------------------------------------------------------------------------
            | PRICE (🔥 FINAL PROMO-AWARE)
            |--------------------------------------------------------------------------
            */
            'price_start_from' => $base > 0 ? $base : null,
            'price_label' => $this->formatPrice($base),

            'original_price' => $original > 0 ? $original : null,
            'original_price_label' => $original > 0 ? $this->formatPrice($original) : null,

            'has_discount' => $original > $base,

            'discount_percent' => $this->calculateDiscount($original, $base),

            'promo_label' => $this->promo_label,

            'discount_label' => $this->promo_label
                ?? ($original > $base ? '-' . $this->calculateDiscount($original, $base) . '%' : null),

            'saving_amount' => $original > $base ? ($original - $base) : 0,

            'saving_label' => $original > $base
                ? 'Hemat Rp' . number_format($original - $base, 0, ',', '.')
                : null,
                
            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */
            'is_active' => (bool) $this->is_active,
            'is_published' => (bool) $this->is_published,

            /*
            |--------------------------------------------------------------------------
            | SEAT INFO (🔥 OTA STYLE)
            |--------------------------------------------------------------------------
            */
            'available_seats' => (int) ($this->available_seats ?? 0),

            'seat_label' => match(true) {
                ($this->available_seats ?? 0) <= 0 => 'Sold Out',
                ($this->available_seats ?? 0) <= 5 => '🔥 Sisa ' . $this->available_seats . ' kursi',
                default => 'Tersedia',
            },

            'is_sold_out' => ($this->available_seats ?? 0) <= 0,

            /*
            |--------------------------------------------------------------------------
            | DEPARTURE
            |--------------------------------------------------------------------------
            */
            'next_departure' => new PaketDepartureSummaryResource(
                $this->whenLoaded('nextDeparture')
            ),

            /*
            |--------------------------------------------------------------------------
            | STATS
            |--------------------------------------------------------------------------
            */
            'departures_count' => (int) ($this->departures_count ?? 0),
            'bookings_count' => (int) ($this->bookings_count ?? 0),

            /*
            |--------------------------------------------------------------------------
            | LINKS
            |--------------------------------------------------------------------------
            */
            'links' => [
                'self' => url('/api/v1/pakets/' . $this->slug),
            ],

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMP
            |--------------------------------------------------------------------------
            */
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
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

    protected function formatText($text): ?string
    {
        return $text ? ucwords(strtolower($text)) : null;
    }

    protected function calculateDiscount(float $original, float $final): int
    {
        if ($original <= 0 || $final <= 0) return 0;
        if ($original <= $final) return 0;

        return (int) round(
            (($original - $final) / $original) * 100
        );
    }
}