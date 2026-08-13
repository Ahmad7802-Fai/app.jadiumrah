<?php

namespace App\Services\Ticketing;

use App\Models\Flight;
use App\Models\PaketDeparture;
use App\Models\SeatAllocation;
use Illuminate\Support\Facades\DB;

class DepartureFlightService
{
    public function assign(
        PaketDeparture $departure,
        Flight $flight,
        string $type = 'departure'
    ) {
        return DB::transaction(function () use ($departure, $flight, $type) {

            /*
            |--------------------------------------------------------------------------
            | 1️⃣ Attach flight ke departure (pivot)
            |--------------------------------------------------------------------------
            */

            $departure->flights()->syncWithoutDetaching([
                $flight->id => ['type' => $type]
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2️⃣ Seat Allocation (create or update)
            |--------------------------------------------------------------------------
            */

            $allocation = SeatAllocation::firstOrNew([
                'flight_id'    => $flight->id,
                'departure_id' => $departure->id,
            ]);

            // Tentukan total seat
            $totalSeat = $flight->aircraft_capacity
                ? min($flight->aircraft_capacity, $departure->quota)
                : $departure->quota;

            $allocation->total_seat   = $totalSeat;
            $allocation->blocked_seat = $allocation->blocked_seat ?? 0;
            $allocation->used_seat    = $departure->booked;

            $allocation->save();


            /*
            |--------------------------------------------------------------------------
            | 3️⃣ Auto Close Logic
            |--------------------------------------------------------------------------
            */

            if ($allocation->used_seat >= $allocation->total_seat) {
                $departure->update(['is_closed' => 1]);
            } else {
                $departure->update(['is_closed' => 0]);
            }

            return $allocation;
        });
    }
}