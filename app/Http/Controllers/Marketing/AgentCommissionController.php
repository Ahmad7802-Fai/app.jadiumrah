<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentCommissionController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth();
        $to   = $request->to ?? now()->endOfMonth();

        $data = Booking::withCount(['jamaahs as total_seat'])
            ->select(
                'agent_id',
                DB::raw('COUNT(id) as total_booking'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'confirmed')
            ->groupBy('agent_id')
            ->with('agent')
            ->get()
            ->map(function ($row) use ($from, $to) {

                // Hitung total seat dari jamaah
                $seat = Booking::where('agent_id', $row->agent_id)
                    ->whereBetween('created_at', [$from, $to])
                    ->where('status', 'confirmed')
                    ->withCount('jamaahs')
                    ->get()
                    ->sum('jamaahs_count');

                $row->total_seat = $seat;

                return $row;
            });

        return view('marketing.agent_commissions.index', compact(
            'data',
            'from',
            'to'
        ));
    }

}