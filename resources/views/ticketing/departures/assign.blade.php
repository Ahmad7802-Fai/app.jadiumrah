@extends('layouts.app')

@section('page-title','Assign Flight')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="card space-y-2">

        <h2 class="text-xl font-bold">
            Assign Flight to Departure
        </h2>

        <div class="text-sm text-gray-500">
            <div>
                <strong>Kode:</strong> {{ $departure->departure_code ?? '-' }}
            </div>
            <div>
                <strong>Tanggal:</strong>
                {{ \Carbon\Carbon::parse($departure->departure_date)->format('d M Y') }}
            </div>
            <div>
                <strong>Quota:</strong> {{ $departure->quota }} seat
            </div>
        </div>

    </div>


    {{-- ================= CURRENT FLIGHT ================= --}}
    <div class="card">

        <h3 class="font-semibold mb-3">Current Assigned Flights</h3>

        @if($departure->flights->count())
            @foreach($departure->flights as $flight)
                <div class="flex justify-between items-center border-b py-2 text-sm">
                    <div>
                        {{ $flight->airline }} -
                        {{ $flight->flight_number }}
                        <span class="text-xs text-gray-400">
                            ({{ strtoupper($flight->pivot->type) }})
                        </span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-gray-400 text-sm">
                No flight assigned yet.
            </div>
        @endif

    </div>


    {{-- ================= ASSIGN FORM ================= --}}
    <div class="card space-y-5">

        <form method="POST"
              action="{{ route('ticketing.departures.assign', $departure) }}"
              class="space-y-5">

            @csrf

            {{-- FLIGHT SELECT --}}
            <div>
                <label class="form-label">Select Flight</label>

                <select name="flight_id"
                        class="form-input"
                        required>
                    <option value="">-- Choose Flight --</option>

                    @foreach($flights as $flight)

                        @php
                            $capacity = $flight->aircraft_capacity ?? '-';
                            $isOverCapacity = $flight->aircraft_capacity
                                && $departure->quota > $flight->aircraft_capacity;
                        @endphp

                        <option value="{{ $flight->id }}"
                            {{ $isOverCapacity ? 'disabled' : '' }}>
                            {{ $flight->airline }}
                            - {{ $flight->flight_number }}
                            (Capacity: {{ $capacity }})
                            {{ $isOverCapacity ? ' - QUOTA EXCEEDS CAPACITY' : '' }}
                        </option>

                    @endforeach
                </select>

                <p class="text-xs text-gray-400 mt-1">
                    Hanya flight aktif & sesuai capacity yang bisa dipilih.
                </p>
            </div>


            {{-- TYPE --}}
            <div>
                <label class="form-label">Flight Type</label>

                <select name="type"
                        class="form-input"
                        required>
                    <option value="departure">Departure</option>
                    <option value="return">Return</option>
                </select>
            </div>


            {{-- ACTION BUTTON --}}
            <div class="flex justify-end gap-3">

                <a href="{{ route('ticketing.departures.index') }}"
                   class="btn-secondary">
                   Cancel
                </a>

                <button class="btn-primary">
                    Assign Flight
                </button>

            </div>

        </form>

    </div>

</div>

@endsection