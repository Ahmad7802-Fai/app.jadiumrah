<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jamaah;
use App\Models\Booking;
use App\Models\CommissionLog;

class AgentController extends Controller
{

    public function stats(Request $request)
    {
        $agentId = $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | TOTAL JAMAAH
        |--------------------------------------------------------------------------
        */

        $totalJamaah = Jamaah::where('agent_id', $agentId)->count();


        /*
        |--------------------------------------------------------------------------
        | JAMAAH AKTIF
        |--------------------------------------------------------------------------
        */

        $jamaahAktif = Jamaah::where('agent_id', $agentId)
            ->where('is_active', true)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL BOOKING
        |--------------------------------------------------------------------------
        */

        $totalBooking = Booking::where('agent_id', $agentId)->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL KOMISI
        |--------------------------------------------------------------------------
        */

        $totalKomisi = CommissionLog::where('agent_id', $agentId)
            ->sum('agent_amount');


        return response()->json([
            'total_jamaah' => $totalJamaah,
            'jamaah_aktif' => $jamaahAktif,
            'total_booking' => $totalBooking,
            'total_komisi' => (int) $totalKomisi
        ]);
    }

}