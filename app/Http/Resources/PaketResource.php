<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        /*
        |--------------------------------------------------------------------------
        | PRICE START FROM (FROM DEPARTURE PRICES)
        |--------------------------------------------------------------------------
        */

        $priceStart = null;

        if ($this->relationLoaded('departures')) {

            $priceStart = $this->departures
                ->flatMap(function ($dep) {
                    return $dep->prices ?? collect();
                })
                ->min('price');
        }

        return [

            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,

            'departure_city' => $this->departure_city,

            'thumbnail' => $this->thumbnail
                ? asset('storage/'.$this->thumbnail)
                : null,

            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            'price_start_from' => $priceStart,


            'duration_days' => $this->duration_days,
            'airline' => $this->airline,
            'short_description' => $this->short_description,


            /*
            |--------------------------------------------------------------------------
            | DEPARTURES
            |--------------------------------------------------------------------------
            */

            'departures' => $this->whenLoaded('departures', function () {

                return $this->departures->map(function ($dep) {

                    return [

                        'id' => $dep->id,

                        'departure_date' => $dep->departure_date,

                        'quota_remaining' =>
                            $dep->quota - $dep->booked,

                    ];

                });

            }),

        ];
    }
}