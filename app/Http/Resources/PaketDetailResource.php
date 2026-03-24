<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | BASIC INFO
            |--------------------------------------------------------------------------
            */

            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'thumbnail' => $this->thumbnail
                ? asset('storage/' . $this->thumbnail)
                : null,

            'gallery' => collect($this->gallery ?? [])
                ->map(fn ($img) => asset('storage/' . $img)),

            'short_description' => $this->short_description,
            'description' => $this->description,

            'duration_days' => $this->duration_days,
            'airline' => $this->airline,

            'price_start_from' => (float) $this->price,


            /*
            |--------------------------------------------------------------------------
            | HOTELS
            |--------------------------------------------------------------------------
            */

            'hotels' => $this->hotels->map(fn ($hotel) => [

                'id' => $hotel->id,
                'city' => $hotel->city,
                'hotel_name' => $hotel->hotel_name,
                'rating' => $hotel->rating,
                'distance_to_haram' => $hotel->distance_to_haram,

            ]),


            /*
            |--------------------------------------------------------------------------
            | ITINERARY
            |--------------------------------------------------------------------------
            */

            'itinerary' => $this->destinations
                ->sortBy('day_order')
                ->map(fn ($item) => [

                    'day_order' => $item->day_order,
                    'destination' => $item->destination?->city,
                    'note' => $item->note,

                ]),


            /*
            |--------------------------------------------------------------------------
            | DEPARTURES
            |--------------------------------------------------------------------------
            */

            'departures' => $this->departures
                ->where('is_active', true)
                ->map(fn ($dep) => [

                    'id' => $dep->id,

                    'departure_date' => $dep->departure_date,
                    'return_date' => $dep->return_date,

                    /*
                    |--------------------------------------------------------------------------
                    | SEAT DATA
                    |--------------------------------------------------------------------------
                    */

                    'quota' => $dep->quota,
                    'booked' => $dep->booked,

                    'quota_remaining' => max(
                        $dep->quota - $dep->booked,
                        0
                    ),

                    /*
                    |--------------------------------------------------------------------------
                    | ROOM PRICES
                    |--------------------------------------------------------------------------
                    */

                    'prices' => $dep->prices->map(fn ($price) => [

                        'room_type' => $price->room_type,
                        'price' => (float) $price->price,

                    ]),

                ]),

        ];
    }
}