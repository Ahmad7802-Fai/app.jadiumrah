<?php

namespace App\Services\Departure;

use App\Models\Paket;
use App\Models\PaketDeparture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DepartureService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE DEPARTURE
    |--------------------------------------------------------------------------
    */

    public function create(array $data): PaketDeparture
    {
        return DB::transaction(function () use ($data) {

            $paket = Paket::findOrFail($data['paket_id']);

            $departure = PaketDeparture::create([
                'paket_id'       => $paket->id,
                'departure_date' => $data['departure_date'],
                'return_date'    => $data['return_date'] ?? null,
                'quota'          => $data['quota'],
                'booked'         => 0,
                'departure_code' => $this->generateCode($paket),
                'flight_number'  => $data['flight_number'] ?? null,
                'meeting_point'  => $data['meeting_point'] ?? null,
                'is_active'      => true,
                'is_closed'      => false,
            ]);

            return $departure;
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

            if ($departure->booked > $data['quota']) {
                throw ValidationException::withMessages([
                    'quota' => 'Quota tidak boleh lebih kecil dari jumlah booked.'
                ]);
            }

            $departure->update([
                'departure_date' => $data['departure_date'],
                'return_date'    => $data['return_date'] ?? null,
                'quota'          => $data['quota'],
                'flight_number'  => $data['flight_number'] ?? null,
                'meeting_point'  => $data['meeting_point'] ?? null,
            ]);

            return $departure;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE ACTIVE
    |--------------------------------------------------------------------------
    */

    public function toggleActive(PaketDeparture $departure): void
    {
        $departure->update([
            'is_active' => !$departure->is_active
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK & AUTO CLOSE
    |--------------------------------------------------------------------------
    */

    public function checkAndAutoClose(PaketDeparture $departure): void
    {
        if ($departure->booked >= $departure->quota) {
            $departure->update([
                'is_closed' => true
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE DEPARTURE CODE
    |--------------------------------------------------------------------------
    */

    private function generateCode(Paket $paket): string
    {
        $count = PaketDeparture::where('paket_id', $paket->id)->count() + 1;

        return 'DEP-' .
            strtoupper(Str::slug($paket->code)) .
            '-' .
            now()->format('Ymd') .
            '-' .
            str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}