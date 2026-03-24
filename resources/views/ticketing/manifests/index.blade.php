@extends('layouts.app')

@section('page-title','Manifest Flight')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border">

    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Flight Manifest</h2>
        <p class="text-sm text-gray-500">
            Generate daftar jamaah per flight
        </p>
    </div>

    <div class="divide-y">

        @foreach($flights as $flight)

            <div class="p-6">

                <div class="flex justify-between items-center mb-3">

                    <div>
                        <div class="font-semibold">
                            {{ $flight->flight_number }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $flight->origin }} → {{ $flight->destination }}
                        </div>
                    </div>

                </div>

                <div class="space-y-2">

                    @foreach($flight->departures as $departure)

                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl">

                            <div class="text-sm">
                                Departure:
                                <span class="font-semibold">
                                    {{ $departure->departure_date }}
                                </span>
                            </div>

                            <div class="flex gap-2">

                                <form method="POST"
                                      action="{{ route('ticketing.manifests.generate') }}">
                                    @csrf
                                    <input type="hidden" name="flight_id"
                                           value="{{ $flight->id }}">
                                    <input type="hidden" name="departure_id"
                                           value="{{ $departure->id }}">

                                    <button class="px-3 py-1 text-xs rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                                        Generate
                                    </button>
                                </form>

                                <a href="#"
                                   class="px-3 py-1 text-xs rounded-lg bg-gray-700 text-white hover:bg-gray-800">
                                    Export PDF
                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endforeach

    </div>

</div>

@endsection