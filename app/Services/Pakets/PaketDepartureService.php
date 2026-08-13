<?php

namespace App\Services\Pakets;

use App\Models\Paket;
use App\Models\PaketDeparture;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaketDepartureService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE DEPARTURE
    |--------------------------------------------------------------------------
    */

    public function create(Paket $paket, array $data): PaketDeparture
    {
        return DB::transaction(function () use ($paket, $data) {

            return $paket->departures()->create([
                'departure_date'  => $data['departure_date'],
                'return_date'     => $data['return_date'] ?? null,
                'quota'           => $data['quota'],
                'booked'          => 0,
                'price_override'  => $data['price_override'] ?? null,
                'is_active'       => $data['is_active'] ?? true,
                'is_closed'       => false,
            ]);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DEPARTURE
    |--------------------------------------------------------------------------
    */

    public function update(PaketDeparture $departure, array $data): PaketDeparture
    {
        return DB::transaction(function () use ($departure, $data) {

            $departure->update([
                'departure_date' => $data['departure_date'],
                'return_date'    => $data['return_date'] ?? null,
                'quota'          => $data['quota'],
                'price_override' => $data['price_override'] ?? null,
                'is_active'      => $data['is_active'] ?? true,
            ]);

            $this->autoCloseIfFull($departure);

            return $departure;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function delete(PaketDeparture $departure): void
    {
        DB::transaction(function () use ($departure) {
            $departure->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | BOOK SEAT
    |--------------------------------------------------------------------------
    */

    public function bookSeat(PaketDeparture $departure, int $qty = 1): void
    {
        DB::transaction(function () use ($departure, $qty) {

            if ($departure->is_closed) {
                throw new \Exception("Keberangkatan sudah ditutup.");
            }

            if ($departure->booked + $qty > $departure->quota) {
                throw new \Exception("Kuota tidak mencukupi.");
            }

            $departure->increment('booked', $qty);

            $this->autoCloseIfFull($departure);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL SEAT
    |--------------------------------------------------------------------------
    */

    public function cancelSeat(PaketDeparture $departure, int $qty = 1): void
    {
        DB::transaction(function () use ($departure, $qty) {

            $departure->decrement('booked', $qty);

            if ($departure->booked < 0) {
                $departure->update(['booked' => 0]);
            }

            if ($departure->booked < $departure->quota) {
                $departure->update(['is_closed' => false]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO CLOSE IF FULL
    |--------------------------------------------------------------------------
    */

    private function autoCloseIfFull(PaketDeparture $departure): void
    {
        if ($departure->booked >= $departure->quota) {
            $departure->update(['is_closed' => true]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO CLOSE IF DATE PASSED
    |--------------------------------------------------------------------------
    */

    public function closeIfExpired(PaketDeparture $departure): void
    {
        if (Carbon::parse($departure->departure_date)->isPast()) {
            $departure->update([
                'is_closed' => true,
                'is_active' => false,
            ]);
        }
    }
}