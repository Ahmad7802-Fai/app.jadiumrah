@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-5 space-y-4">

    {{-- HEADER --}}
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                {{ $paket->name }}
            </h1>
            <p class="text-xs text-gray-400">
                {{ $paket->code }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('pakets.edit',$paket) }}"
               class="btn btn-xs btn-outline">Edit</a>

            <a href="{{ route('pakets.index') }}"
               class="btn btn-xs btn-secondary">Back</a>
        </div>

    </div>


    {{-- TOP GRID --}}
    <div class="grid lg:grid-cols-3 gap-4">

        {{-- LEFT --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- INFO --}}
            <div class="card-compact p-4 space-y-3 text-sm">

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs">

                    <div>
                        <div class="text-gray-400">Kota</div>
                        <div class="font-medium">{{ $paket->departure_city ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-400">Maskapai</div>
                        <div class="font-medium">{{ $paket->airline ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-400">Durasi</div>
                        <div class="font-medium">{{ $paket->duration_days ?? '-' }} Hari</div>
                    </div>

                </div>

                <div class="pt-2 border-t text-xs">
                    <div class="text-gray-400 mb-1">Short</div>
                    <div>{{ $paket->short_description ?? '-' }}</div>
                </div>

                <div class="text-xs">
                    <div class="text-gray-400 mb-1">Deskripsi</div>
                    <div class="leading-relaxed">
                        {{ $paket->description ?? '-' }}
                    </div>
                </div>

            </div>


            {{-- HOTEL --}}
            <div class="card-compact p-4 space-y-2">

                <h3 class="text-sm font-semibold text-gray-700">Hotel</h3>

                <div class="grid md:grid-cols-2 gap-2 text-xs">

                    @foreach($paket->hotels as $hotel)
                        <div class="border rounded-lg p-2 bg-gray-50">

                            <div class="font-medium text-gray-800">
                                {{ $hotel->hotel_name }}
                            </div>

                            <div class="text-gray-500 text-[11px]">
                                {{ ucfirst($hotel->city) }} • ⭐ {{ $hotel->rating }}
                            </div>

                            <div class="text-gray-400 text-[10px]">
                                {{ $hotel->distance_to_haram }}
                            </div>

                        </div>
                    @endforeach

                </div>

            </div>


            {{-- ITINERARY --}}
            <div class="card-compact p-4 space-y-2">

                <h3 class="text-sm font-semibold text-gray-700">Itinerary</h3>

                <div class="space-y-1 text-xs">

                    @foreach($paket->itinerary as $item)
                        <div class="flex justify-between border-b py-1">

                            <div>
                                <span class="font-medium">
                                    H{{ $item->day_order }}
                                </span>
                                — {{ $item->destination->city }}
                            </div>

                            <div class="text-gray-400">
                                {{ $item->note }}
                            </div>

                        </div>
                    @endforeach

                </div>

            </div>


            {{-- DEPARTURE --}}
            <div class="card-compact p-4 space-y-2">

                <h3 class="text-sm font-semibold text-gray-700">Departure</h3>

                <div class="space-y-2 text-xs">

                    @foreach($paket->departures as $dep)

                        <div class="border rounded-lg p-2 bg-gray-50">

                            <div class="flex justify-between mb-1">

                                <div class="font-medium text-gray-700">
                                    {{ \Carbon\Carbon::parse($dep->departure_date)->format('d M') }}
                                    -
                                    {{ \Carbon\Carbon::parse($dep->return_date)->format('d M') }}
                                </div>

                                <div class="text-gray-500">
                                    Q: {{ $dep->quota }}
                                </div>

                            </div>

                            <div class="flex flex-wrap gap-2 text-[10px] text-gray-600">

                                @foreach($dep->prices as $price)
                                    <div>
                                        {{ substr($price->room_type,0,3) }} :
                                        <span class="font-medium">
                                            {{ number_format($price->price,0,',','.') }}
                                        </span>
                                    </div>
                                @endforeach

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>


        {{-- RIGHT --}}
        <div class="space-y-4">

            {{-- STATUS --}}
            <div class="card-compact p-3 space-y-2 text-xs">

                <div class="text-gray-400">Status</div>

                <div class="flex gap-1 flex-wrap">

                    <span class="px-2 py-0.5 rounded-full text-[10px]
                        {{ $paket->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $paket->is_active ? 'Active' : 'Off' }}
                    </span>

                    <span class="px-2 py-0.5 rounded-full text-[10px]
                        {{ $paket->is_published ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $paket->is_published ? 'Publish' : 'Draft' }}
                    </span>

                </div>

            </div>


            {{-- THUMB --}}
            @if($paket->thumbnail)
            <div class="card-compact p-2">
                <img src="{{ asset('storage/'.$paket->thumbnail) }}"
                     class="w-full rounded-lg">
            </div>
            @endif

        </div>

    </div>

</div>

@endsection