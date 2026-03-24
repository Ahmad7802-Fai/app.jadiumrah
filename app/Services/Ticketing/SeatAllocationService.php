<?php 

namespace App\Services\Ticketing;

use App\Models\SeatAllocation;
use Illuminate\Support\Facades\DB;
use Exception;

class SeatAllocationService
{
    public function allocate($flightId, $departureId, $seat)
    {
        return DB::transaction(function () use ($flightId, $departureId, $seat) {

            $allocation = SeatAllocation::where([
                'flight_id'    => $flightId,
                'departure_id' => $departureId
            ])->lockForUpdate()->firstOrFail();

            if ($allocation->available_seat < $seat) {
                throw new Exception("Seat tidak mencukupi.");
            }

            $allocation->increment('used_seat', $seat);

            return $allocation;
        });
    }

    public function release($flightId, $departureId, $seat)
    {
        $allocation = SeatAllocation::where([
            'flight_id'    => $flightId,
            'departure_id' => $departureId
        ])->firstOrFail();

        $allocation->decrement('used_seat', $seat);
    }
}