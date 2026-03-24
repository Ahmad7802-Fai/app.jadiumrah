<?php

namespace App\Services\Ticketing;

use App\Models\Flight;
use App\Models\FlightSegment;
use Illuminate\Support\Facades\DB;

class FlightService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE FLIGHT
    |--------------------------------------------------------------------------
    */
    public function create(array $data, array $segments = []): Flight
    {
        return DB::transaction(function () use ($data, $segments) {

            $flight = Flight::create([
                'airline'           => $data['airline'],
                'flight_number'     => $data['flight_number'],
                'aircraft_type'     => $data['aircraft_type'] ?? null,
                'aircraft_capacity' => $data['aircraft_capacity'] ?? null,
                'is_charter'        => $data['is_charter'] ?? false,
                'is_active'         => $data['is_active'] ?? true,
                'notes'             => $data['notes'] ?? null,
            ]);

            if (!empty($segments)) {
                $this->syncSegments($flight, $segments);
            }

            return $flight;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE FLIGHT
    |--------------------------------------------------------------------------
    */
    public function update(Flight $flight, array $data, array $segments = []): Flight
    {
        return DB::transaction(function () use ($flight, $data, $segments) {

            $flight->update([
                'airline'           => $data['airline'],
                'flight_number'     => $data['flight_number'],
                'aircraft_type'     => $data['aircraft_type'] ?? null,
                'aircraft_capacity' => $data['aircraft_capacity'] ?? null,
                'is_charter'        => $data['is_charter'] ?? false,
                'is_active'         => $data['is_active'] ?? true,
                'notes'             => $data['notes'] ?? null,
            ]);

            // Reset segments
            $flight->segments()->delete();

            if (!empty($segments)) {
                $this->syncSegments($flight, $segments);
            }

            return $flight->fresh(['segments']);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE FLIGHT
    |--------------------------------------------------------------------------
    */
    public function delete(Flight $flight): void
    {
        DB::transaction(function () use ($flight) {
            $flight->segments()->delete();
            $flight->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE / DEACTIVATE FLIGHT
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(Flight $flight): Flight
    {
        $flight->update([
            'is_active' => !$flight->is_active
        ]);

        return $flight;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: SYNC SEGMENTS
    |--------------------------------------------------------------------------
    */
    private function syncSegments(Flight $flight, array $segments): void
    {
        foreach ($segments as $index => $segment) {

            if (empty($segment['origin']) || empty($segment['destination'])) {
                continue;
            }

            $flight->segments()->create([
                'segment_order'  => $index + 1,
                'origin'         => $segment['origin'],
                'destination'    => $segment['destination'],
                'departure_time' => $segment['departure_time'],
                'arrival_time'   => $segment['arrival_time'],
                'terminal'       => $segment['terminal'] ?? null,
                'gate'           => $segment['gate'] ?? null,
            ]);
        }
    }
}