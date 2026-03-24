<?php

namespace App\Http\Resources\Api\V1\Paket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaketItineraryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_order' => (int) $this->day_order,
            'destination' => $this->destination ? [
                'id' => $this->destination->id,
                'name' => $this->destination->city,
                'city' => $this->destination->city,
                'country' => $this->destination->country,
                'type' => $this->destination->type,
            ] : null,
            'note' => $this->note,
        ];
    }
}