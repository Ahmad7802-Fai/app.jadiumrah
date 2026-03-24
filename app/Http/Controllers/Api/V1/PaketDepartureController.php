<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaketDeparture;

class PaketDepartureController extends Controller
{
    public function show(PaketDeparture $departure)
    {
        $departure->load([
            'prices',
            'paket'
        ]);

        $remaining = max(0, $departure->quota - $departure->booked);

        return response()->json([
            'data' => [
                'id' => $departure->id,

                'paket_id' => $departure->paket_id,

                'departure_date' => $departure->departure_date,

                'quota' => $departure->quota,

                'booked' => $departure->booked,

                'quota_remaining' => $remaining,

                'is_closed' => $departure->is_closed,

                'is_available' => !$departure->is_closed && $remaining > 0,

                'prices' => $departure->prices->map(function ($price) {

                    return [
                        'room_type' => $price->room_type,
                        'price' => (float) $price->price,
                    ];

                })->values(),
            ]
        ]);
    }

    public function byPaket($paketId)
    {
        $departures = PaketDeparture::with('prices')
            ->where('paket_id', $paketId)
            ->orderBy('departure_date')
            ->get();

        return response()->json([
            'data' => $departures->map(function ($departure) {

                $remaining = max(0, $departure->quota - $departure->booked);

                return [

                    'id' => $departure->id,

                    'paket_id' => $departure->paket_id,

                    'departure_date' => $departure->departure_date,

                    'return_date' => $departure->return_date,

                    'quota' => $departure->quota,

                    'booked' => $departure->booked,

                    'quota_remaining' => $remaining,

                    'is_closed' => $departure->is_closed,

                    'is_available' => !$departure->is_closed && $remaining > 0,

                    'prices' => $departure->prices->map(function ($price) {

                        return [
                            'room_type' => $price->room_type,
                            'price' => (float) $price->price
                        ];

                    })->values()

                ];

            })
        ]);
    }

}