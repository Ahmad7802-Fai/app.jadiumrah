@extends('layouts.app')

@section('page-title','Seat Allocation')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border">

    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Seat Allocation</h2>
        <p class="text-sm text-gray-500">
            Kontrol overbooking & utilization
        </p>
    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 text-left">Flight</th>
                    <th class="px-6 py-3 text-left">Departure</th>
                    <th class="px-6 py-3 text-center">Total Seat</th>
                    <th class="px-6 py-3 text-center">Used</th>
                    <th class="px-6 py-3 text-center">Available</th>
                    <th class="px-6 py-3 text-center">Utilization</th>
                </tr>
            </thead>

            <tbody class="divide-y">

            @foreach($allocations as $row)

                @php
                    $available = $row->total_seat - $row->used_seat;
                    $util = $row->total_seat > 0
                        ? round(($row->used_seat / $row->total_seat) * 100)
                        : 0;
                @endphp

                <tr class="hover:bg-gray-50">

                    <td class="px-6 py-4">
                        <div class="font-semibold">
                            {{ $row->flight->flight_number }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $row->flight->origin }} → {{ $row->flight->destination }}
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        {{ $row->departure->departure_date ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-center font-semibold">
                        {{ $row->total_seat }}
                    </td>

                    <td class="px-6 py-4 text-center text-blue-600 font-semibold">
                        {{ $row->used_seat }}
                    </td>

                    <td class="px-6 py-4 text-center text-green-600 font-semibold">
                        {{ $available }}
                    </td>

                    <td class="px-6 py-4 text-center">

                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-primary-600 h-2 rounded-full"
                                 style="width: {{ $util }}%">
                            </div>
                        </div>

                        <span class="text-xs font-semibold">
                            {{ $util }}%
                        </span>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

    <div class="p-4">
        {{ $allocations->links() }}
    </div>

</div>

@endsection