@extends('layouts.app')

@section('page-title','Data Flight')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold">
                Data Flight
            </h2>
            <p class="text-sm text-gray-500">
                Master airline & aircraft configuration
            </p>
        </div>

        <a href="{{ route('ticketing.flights.create') }}"
           class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm shadow">
            + Tambah Flight
        </a>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Airline</th>
                    <th class="px-4 py-3 text-left">Flight</th>
                    <th class="px-4 py-3 text-left">Route</th>
                    <th class="px-4 py-3 text-left">Capacity</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($flights as $flight)

                @php
                    $segments = $flight->segments;
                    $firstSegment = $segments->first();
                    $lastSegment = $segments->last();

                    $route = $firstSegment && $lastSegment
                        ? $firstSegment->origin . ' → ' . $lastSegment->destination
                        : '-';
                @endphp

                <tr class="border-t hover:bg-gray-50 transition">
                    {{-- Airline --}}
                    <td class="px-4 py-3">
                        {{ $flight->airline }}
                    </td>

                    {{-- Flight Number --}}
                    <td class="px-4 py-3 font-medium">
                        {{ $flight->flight_number }}
                    </td>

                    {{-- Route --}}
                    <td class="px-4 py-3">
                        {{ $route }}

                        @if($segments->count() > 1)
                            <div class="text-xs text-gray-400">
                                {{ $segments->count() }} segments
                            </div>
                        @endif
                    </td>

                    {{-- Capacity --}}
                    <td class="px-4 py-3">
                        {{ $flight->aircraft_capacity ?? '-' }}
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-3">
                        @if($flight->is_charter)
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                Charter
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                Regular
                            </span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @if($flight->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                Inactive
                            </span>
                        @endif
                    </td>

                    {{-- ACTION --}}
                    <td class="px-4 py-3 text-center space-x-3">

                        <a href="{{ route('ticketing.flights.edit', $flight) }}"
                           class="text-blue-600 hover:underline text-xs">
                            Edit
                        </a>

                        <form action="{{ route('ticketing.flights.destroy', $flight) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Delete this flight?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        Belum ada flight
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    {{-- ================= PAGINATION ================= --}}
    <div>
        {{ $flights->links() }}
    </div>

</div>

@endsection