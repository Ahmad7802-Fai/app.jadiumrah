<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'status' => $this->status,

            'expired_at' => $this->expired_at,
            'created_at' => $this->created_at,

            'room_type' => $this->room_type,
            'qty' => $this->qty,

            'price_per_person_snapshot' => (float) $this->price_per_person_snapshot,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining' => (float) $this->remaining,

            'paket' => [
                'id' => $this->paket?->id,
                'name' => $this->paket?->name,
            ],

            'departure' => [
                'id' => $this->departure?->id,
                'departure_date' => $this->departure?->departure_date,
            ],

            'jamaahs' => $this->jamaahs->map(function ($jamaah) {
                return [
                    'id' => $jamaah->id,
                    'nama_lengkap' => $jamaah->nama_lengkap,
                    'room_type' => $jamaah->pivot->room_type,
                    'price_snapshot' => (float) $jamaah->pivot->price,
                ];
            }),
        ];
    }
}