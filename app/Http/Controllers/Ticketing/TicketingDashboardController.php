<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\PaketDeparture;
use App\Models\SeatAllocation;
use Illuminate\Support\Facades\DB;

class TicketingDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | SUMMARY (Hanya departure aktif)
        |--------------------------------------------------------------------------
        */

        $summary = SeatAllocation::join('paket_departures', 'paket_departures.id', '=', 'seat_allocations.departure_id')
            ->where('paket_departures.is_active', 1)
            ->select(
                DB::raw('COUNT(DISTINCT seat_allocations.flight_id) as total_flight'),
                DB::raw('COUNT(DISTINCT seat_allocations.departure_id) as total_departure'),
                DB::raw('SUM(seat_allocations.total_seat) as total_seat'),
                DB::raw('SUM(seat_allocations.used_seat) as used_seat'),
                DB::raw('SUM(seat_allocations.total_seat - seat_allocations.blocked_seat - seat_allocations.used_seat) as available_seat')
            )
            ->first();

        $utilization = 0;

        if ($summary && $summary->total_seat > 0) {
            $utilization = round(
                ($summary->used_seat / $summary->total_seat) * 100,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DETAIL PER DEPARTURE
        |--------------------------------------------------------------------------
        */

        $details = SeatAllocation::with(['flight','departure'])
            ->whereHas('departure', function($q){
                $q->where('is_active', 1);
            })
            ->latest()
            ->get()
            ->map(function ($row) {

                $available = $row->total_seat - $row->blocked_seat - $row->used_seat;

                $row->utilization = $row->total_seat > 0
                    ? round(($row->used_seat / $row->total_seat) * 100, 1)
                    : 0;

                $row->available_seat = $available;

                // Status otomatis
                if ($row->utilization >= 100) {
                    $row->status = 'FULL';
                } elseif ($row->utilization >= 85) {
                    $row->status = 'CRITICAL';
                } else {
                    $row->status = 'NORMAL';
                }

                return $row;
            });

        /*
        |--------------------------------------------------------------------------
        | UPCOMING DEPARTURE OVERVIEW
        |--------------------------------------------------------------------------
        */

        $upcomingDepartures = PaketDeparture::where('is_active', 1)
            ->where('departure_date', '>=', now())
            ->orderBy('departure_date')
            ->limit(5)
            ->get();

        return view('ticketing.dashboard.index', compact(
            'summary',
            'utilization',
            'details',
            'upcomingDepartures'
        ));
    }
}