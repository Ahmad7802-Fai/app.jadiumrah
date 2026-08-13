<?php

namespace App\Services\Manifest;

use App\Models\PaketDeparture;
use Illuminate\Support\Facades\DB;

class SeatGeneratorService
{
    public function generate(PaketDeparture $departure): void
    {
        DB::transaction(function () use ($departure) {

            $departure->load('bookings.jamaahs');

            $jamaahs = $departure->bookings
                ->flatMap(fn ($booking) => $booking->jamaahs)
                ->unique('id')
                ->values();

            if ($jamaahs->isEmpty()) {
                return;
            }

            // Reset dulu seat lama
            $jamaahs->each->update(['seat_number' => null]);

            $row = 1;
            $letters = ['A','B','C','D'];
            $index = 0;

            foreach ($jamaahs as $jamaah) {

                $seat = $row . $letters[$index];

                $jamaah->update([
                    'seat_number' => $seat
                ]);

                $index++;

                if ($index >= count($letters)) {
                    $index = 0;
                    $row++;
                }
            }
        });
    }
}