@extends('layouts.app')

@section('page-title','Departure Management')

@section('content')

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold">
                Paket Departures
            </h1>
            <p class="text-sm text-gray-500">
                Kelola keberangkatan & assignment flight
            </p>
        </div>

        @can('create', \App\Models\PaketDeparture::class)
        <a href="{{ route('ticketing.departures.create') }}"
           class="btn-primary">
            + Create Departure
        </a>
        @endcan

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card">

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Paket</th>
                        <th>Tanggal</th>
                        <th>Quota</th>
                        <th>Flight</th>
                        <th>Status</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($departures as $departure)

                    @php
                        $percent = $departure->quota > 0
                            ? round(($departure->booked / $departure->quota) * 100, 2)
                            : 0;
                    @endphp

                    <tr>

                        {{-- CODE --}}
                        <td class="font-medium">
                            {{ $departure->departure_code ?? '-' }}
                        </td>

                        {{-- PAKET --}}
                        <td>
                            {{ $departure->paket->name ?? '-' }}
                        </td>

                        {{-- DATE --}}
                        <td>
                            {{ \Carbon\Carbon::parse($departure->departure_date)->format('d M Y') }}
                            <div class="text-xs text-gray-400">
                                Return:
                                {{ $departure->return_date
                                    ? \Carbon\Carbon::parse($departure->return_date)->format('d M Y')
                                    : '-' }}
                            </div>
                        </td>

                        {{-- QUOTA PROGRESS --}}
                        <td class="min-w-[180px]">

                            <div class="text-sm font-medium">
                                {{ $departure->booked }} / {{ $departure->quota }}
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="h-2 rounded-full
                                    {{ $percent >= 80 ? 'bg-red-500' : 'bg-primary-600' }}"
                                    style="width: {{ $percent }}%">
                                </div>
                            </div>

                            <div class="text-xs text-gray-400 mt-1">
                                {{ $percent }}% booked
                            </div>

                        </td>

                        {{-- FLIGHT LIST --}}
                        <td>
                            @if($departure->flights->count())
                                @foreach($departure->flights as $flight)
                                    <div class="text-sm">
                                        {{ $flight->airline }} -
                                        {{ $flight->flight_number }}
                                        <span class="text-xs text-gray-400">
                                            ({{ strtoupper($flight->pivot->type) }})
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-gray-400 text-sm">
                                    Not assigned
                                </span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td>
                            @if($departure->is_closed)
                                <span class="badge-red">CLOSED</span>
                            @elseif(!$departure->is_active)
                                <span class="badge-gray">INACTIVE</span>
                            @else
                                <span class="badge-green">OPEN</span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="flex flex-wrap gap-2">

                            @can('update', $departure)
                            <a href="{{ route('ticketing.departures.edit', $departure) }}"
                               class="btn-sm-secondary">
                                Edit
                            </a>

                            <a href="{{ route('ticketing.departures.assign-form', $departure) }}"
                               class="btn-sm-primary">
                                Assign
                            </a>
                            @endcan

                            @can('delete', $departure)
                            <form action="{{ route('ticketing.departures.destroy', $departure) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this departure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-sm-danger">
                                    Delete
                                </button>
                            </form>
                            @endcan

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7"
                            class="text-center text-gray-400 py-6">
                            No departures found.
                        </td>
                    </tr>

                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $departures->links() }}
        </div>

    </div>

</div>

@endsection