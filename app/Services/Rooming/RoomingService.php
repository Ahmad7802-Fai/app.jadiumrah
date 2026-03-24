<?php

namespace App\Services\Rooming;

use App\Models\PaketDeparture;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomingService
{
    public function generate(
        PaketDeparture $departure,
        string $city = 'makkah',
        ?string $hotelName = null
    ): void {

        DB::transaction(function () use ($departure, $city, $hotelName) {

            $jamaahs = $this->getApprovedJamaahs($departure);

            if ($jamaahs->isEmpty()) {
                return;
            }

            // 🔥 Pisahkan gender
            $males   = $jamaahs->where('gender_normalized','male')->values();
            $females = $jamaahs->where('gender_normalized','female')->values();

            $this->processGender($departure, $males, 'male', $city, $hotelName);
            $this->processGender($departure, $females, 'female', $city, $hotelName);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GET APPROVED + NORMALIZE
    |--------------------------------------------------------------------------
    */
    private function getApprovedJamaahs(PaketDeparture $departure)
    {
        return $departure->bookings()
            ->with('jamaahs')
            ->get()
            ->flatMap(function ($booking) {

                return $booking->jamaahs->map(function ($jamaah) use ($booking) {

                    $pivot = $booking->jamaahs()
                        ->where('jamaah_id', $jamaah->id)
                        ->first()
                        ->pivot;

                    $jamaah->room_type = $pivot->room_type ?? 'quad';

                    // normalize gender
                    $gender = strtolower($jamaah->gender ?? 'male');

                    $jamaah->gender_normalized =
                        in_array($gender,['p','female'])
                            ? 'female'
                            : 'male';

                    return $jamaah;
                });
            })
            ->where('approval_status','approved')
            ->unique('id')
            ->filter(fn($j) => $j->rooms()->count() === 0)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS PER GENDER
    |--------------------------------------------------------------------------
    */
    private function processGender(
        PaketDeparture $departure,
        $jamaahs,
        string $gender,
        string $city,
        ?string $hotelName
    ): void {

        if ($jamaahs->isEmpty()) {
            return;
        }

        // 🔥 1️⃣ Group Family First
        $families = $jamaahs
            ->whereNotNull('family_id')
            ->groupBy('family_id');

        foreach ($families as $family) {
            $this->generateByRoomType(
                $departure,
                $family->values(),
                $gender,
                $city,
                $hotelName
            );
        }

        // 🔥 2️⃣ Couples (double only)
        $couples = $jamaahs
            ->whereNull('family_id')
            ->where('room_type','double')
            ->values()
            ->chunk(2);

        foreach ($couples as $pair) {
            if ($pair->count() === 2) {
                $this->createRoom(
                    $departure,
                    $pair,
                    $gender,
                    2,
                    $city,
                    $hotelName,
                    null
                );
            }
        }

        // 🔥 3️⃣ Remaining
        $remaining = $jamaahs
            ->filter(fn($j) =>
                empty($j->family_id)
            )
            ->values();

        $this->generateByRoomType(
            $departure,
            $remaining,
            $gender,
            $city,
            $hotelName
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE BY ROOM TYPE
    |--------------------------------------------------------------------------
    */
    private function generateByRoomType(
        PaketDeparture $departure,
        $jamaahs,
        string $gender,
        string $city,
        ?string $hotelName
    ): void {

        $grouped = $jamaahs->groupBy('room_type');

        foreach ($grouped as $roomType => $group) {

            $capacity = match($roomType) {
                'double' => 2,
                'triple' => 3,
                'quad'   => 4,
                default  => 4
            };

            $this->generateSmart(
                $departure,
                $group->values(),
                $gender,
                $capacity,
                $city,
                $hotelName
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SMART BALANCED FILL
    |--------------------------------------------------------------------------
    */
    private function generateSmart(
        PaketDeparture $departure,
        $jamaahs,
        string $gender,
        int $capacity,
        string $city,
        ?string $hotelName
    ): void {

        if ($jamaahs->isEmpty()) {
            return;
        }

        $total = $jamaahs->count();
        $roomsNeeded = (int) ceil($total / $capacity);

        $base = intdiv($total, $roomsNeeded);
        $remainder = $total % $roomsNeeded;

        $index = 0;

        for ($i = 0; $i < $roomsNeeded; $i++) {

            $fill = $base + ($remainder-- > 0 ? 1 : 0);

            $slice = $jamaahs->slice($index, $fill)->values();

            $this->createRoom(
                $departure,
                $slice,
                $gender,
                $capacity,
                $city,
                $hotelName,
                null
            );

            $index += $fill;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ROOM
    |--------------------------------------------------------------------------
    */
    private function createRoom(
        PaketDeparture $departure,
        $jamaahGroup,
        string $gender,
        int $capacity,
        string $city,
        ?string $hotelName,
        ?int $roomNumber
    ): void {

        $roomCount = $departure->rooms()
            ->where('gender',$gender)
            ->count() + 1;

        $room = Room::create([
            'departure_id' => $departure->id,
            'hotel_name'   => $hotelName,
            'city'         => $city,
            'room_number'  => strtoupper(substr($gender,0,1)) . $roomCount,
            'gender'       => $gender,
            'capacity'     => $capacity,
        ]);

        foreach ($jamaahGroup as $jamaah) {
            $room->jamaahs()->attach($jamaah->id);
        }
    }
}