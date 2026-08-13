<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketDetailResource extends JsonResource
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
            'duration_days' => $this->duration_days !== null ? (int) $this->duration_days : null,
            'duration_label' => $this->duration_days !== null ? ((int) $this->duration_days . ' Hari') : null,
            'airline' => $this->formatText($this->airline),
            'short_description' => $this->short_description,
            'description' => $this->description,

            /*
            |--------------------------------------------------------------------------
            | NEXT DEPARTURE
            |--------------------------------------------------------------------------
            */
            'next_departure_date' => optional($this->nextDeparture)?->departure_date?->toJSON(),
            'next_return_date' => optional($this->nextDeparture)?->return_date?->toJSON(),

            /*
            |--------------------------------------------------------------------------
            | MEDIA
            |--------------------------------------------------------------------------
            */
            'thumbnail' => $this->thumbnail_url,
            'gallery' => $this->gallery_urls,

            /*
            |--------------------------------------------------------------------------
            | PRICE (🔥 PROMO-AWARE)
            |--------------------------------------------------------------------------
            */
            'price_start_from' => $base > 0 ? $base : null,
            'price_label' => $this->formatStartFromPrice($base),

            'original_price' => $original > 0 ? $original : null,
            'original_price_label' => $original > 0 ? $this->formatPrice($original) : null,

            'has_discount' => $original > $base,

            'discount_percent' => $this->calculateDiscount($original, $base),

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
            | RELATIONS
            |--------------------------------------------------------------------------
            */
            'hotels' => PaketHotelResource::collection($this->whenLoaded('hotels')),
            'itinerary' => PaketItineraryResource::collection($this->whenLoaded('itinerary')),
            'departures' => PaketDepartureResource::collection($this->whenLoaded('departures')),

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
            'created_at' => optional($this->created_at)?->toJSON(),
            'updated_at' => optional($this->updated_at)?->toJSON(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function formatStartFromPrice($price): ?string
    {
        if ($price === null || $price <= 0) return null;

        return 'Mulai dari Rp' . number_format((float) $price, 0, ',', '.');
    }

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