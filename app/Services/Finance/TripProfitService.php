<?php

namespace App\Services\Finance;

use App\Models\PaketDeparture;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Cost;
use App\Models\CommissionLog;

class TripProfitService
{
    public function calculate(int $departureId): array
    {
        $departure = PaketDeparture::findOrFail($departureId);

        /*
        |--------------------------------------------------------------------------
        | BOOKINGS
        |--------------------------------------------------------------------------
        */

        $bookings = Booking::where('paket_departure_id',$departureId)
            ->where('status','confirmed')
            ->get();

        $revenue = $bookings->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | PAYMENTS RECEIVED
        |--------------------------------------------------------------------------
        */

        $payments = Payment::where('paket_departure_id',$departureId)
            ->where('status','paid')
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | COST
        |--------------------------------------------------------------------------
        */

        $cost = Cost::where('paket_departure_id',$departureId)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | COMMISSION
        |--------------------------------------------------------------------------
        */

        $commission = CommissionLog::where('paket_departure_id',$departureId)
            ->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | PROFIT
        |--------------------------------------------------------------------------
        */

        $profit = $payments - $cost - $commission;

        return [

            'departure_id' => $departureId,

            'revenue' => $revenue,

            'payments_received' => $payments,

            'cost' => $cost,

            'commission' => $commission,

            'profit' => $profit,

            'margin' => $payments > 0
                ? round(($profit / $payments) * 100,2)
                : 0,
        ];
    }
}