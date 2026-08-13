<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketHotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'city' => $this->city ? ucfirst($this->city) : null,
            'hotel_name' => $this->hotel_name,
            'rating' => $this->rating !== null ? (int) $this->rating : null,
            'distance_to_haram' => $this->distance_to_haram,
        ];
    }
}