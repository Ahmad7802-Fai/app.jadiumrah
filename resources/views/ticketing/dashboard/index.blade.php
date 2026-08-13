@extends('layouts.app')

@section('page-title','Ticketing Dashboard')

@section('content')

<div class="space-y-8">

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="grid md:grid-cols-5 gap-6">

        <div class="card-compact">
            <div class="meta-label">Total Departure</div>
            <div class="text-2xl font-bold mt-2">
                {{ $summary->total_departure ?? 0 }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Total Flight</div>
            <div class="text-2xl font-bold mt-2">
                {{ $summary->total_flight ?? 0 }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Total Seat</div>
            <div class="text-2xl font-bold mt-2">
                {{ number_format($summary->total_seat ?? 0) }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Used Seat</div>
            <div class="text-2xl font-bold text-red-600 mt-2">
                {{ number_format($summary->used_seat ?? 0) }}
            </div>
        </div>

        <div class="card-compact">
            <div class="meta-label">Available Seat</div>
            <div class="text-2xl font-bold text-green-600 mt-2">
                {{ number_format($summary->available_seat ?? 0) }}
            </div>
        </div>

    </div>


    {{-- ================= UTILIZATION BAR ================= --}}
    <div class="card">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">
                Global Seat Utilization
            </h2>

            <span class="text-sm font-semibold">
                {{ $utilization }}%
            </span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">

            <div class="h-4 rounded-full transition-all duration-500
                {{ $utilization >= 90 ? 'bg-red-600'
                   : ($utilization >= 75 ? 'bg-yellow-500'
                   : 'bg-primary-600') }}"
                style="width: {{ $utilization }}%">
            </div>

        </div>

    </div>


    {{-- ================= DETAIL TABLE ================= --}}
    <div class="card">

        <h2 class="text-lg font-semibold mb-4">
            Seat Allocation per Departure
        </h2>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Flight</th>
                        <th>Departure Date</th>
                        <th>Total</th>
                        <th>Used</th>
                        <th>Available</th>
                        <th>Utilization</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($details as $row)

                    <tr class="{{ $row->status === 'FULL' ? 'bg-red-50' : '' }}">

                        <td>
                            <div class="font-semibold">
                                {{ $row->flight->flight_number ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $row->flight->airline ?? '' }}
                            </div>
                        </td>

                        <td>
                            {{ optional($row->departure)->departure_date }}
                        </td>

                        <td>{{ $row->total_seat }}</td>

                        <td class="text-red-600 font-semibold">
                            {{ $row->used_seat }}
                        </td>

                        <td class="text-green-600 font-semibold">
                            {{ $row->available_seat }}
                        </td>

                        <td>
                            <span class="
                                {{ $row->utilization >= 90 ? 'text-red-600 font-bold'
                                   : ($row->utilization >= 75 ? 'text-yellow-600 font-semibold'
                                   : '') }}">
                                {{ $row->utilization }}%
                            </span>
                        </td>

                        <td>
                            @if($row->status === 'FULL')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-600 text-white">
                                    FULL
                                </span>
                            @elseif($row->status === 'CRITICAL')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-white">
                                    CRITICAL
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">
                                    NORMAL
                                </span>
                            @endif
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">
                            Belum ada seat allocation
                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection