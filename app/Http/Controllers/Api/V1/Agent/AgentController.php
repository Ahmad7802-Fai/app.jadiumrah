<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CommissionLog;
use App\Models\Jamaah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $agentId = $request->user()->id;

        $totalJamaah = Jamaah::query()
            ->where('agent_id', $agentId)
            ->count();

        $jamaahAktif = Jamaah::query()
            ->where('agent_id', $agentId)
            ->where('is_active', true)
            ->count();

        $totalBooking = Booking::query()
            ->where('agent_id', $agentId)
            ->count();

        $totalKomisi = CommissionLog::query()
            ->where('agent_id', $agentId)
            ->sum('agent_amount');

        return response()->json([
            'success' => true,
            'message' => 'Statistik agent berhasil diambil.',
            'data' => [
                'total_jamaah'  => (int) $totalJamaah,
                'jamaah_aktif'  => (int) $jamaahAktif,
                'total_booking' => (int) $totalBooking,
                'total_komisi'  => (float) $totalKomisi,
            ],
        ]);
    }
}