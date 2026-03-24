@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                {{ $paket->name }}
            </h1>
            <p class="text-sm text-gray-500">
                Code: {{ $paket->code }}
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('pakets.edit',$paket) }}" class="btn btn-outline">
                Edit
            </a>
            <a href="{{ route('pakets.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>


    {{-- ================= TOP GRID ================= --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ================= LEFT (DETAIL) ================= --}}
        <div class="lg:col-span-2 card space-y-5">

            <div class="grid md:grid-cols-2 gap-5 text-sm">

                <div>
                    <div class="text-gray-500">Kota</div>
                    <div class="font-medium">{{ $paket->departure_city ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-gray-500">Maskapai</div>
                    <div class="font-medium">{{ $paket->airline ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-gray-500">Harga Base</div>
                    <div class="font-semibold text-primary">
                        Rp {{ number_format($paket->price,0,',','.') }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Durasi</div>
                    <div class="font-medium">
                        {{ $paket->duration_days ?? '-' }} Hari
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Quota Default</div>
                    <div class="font-medium">{{ $paket->quota ?? '-' }}</div>
                </div>

            </div>

            <div class="pt-4 border-t">
                <div class="text-gray-500 text-sm mb-1">Deskripsi Singkat</div>
                <div class="text-sm">{{ $paket->short_description ?? '-' }}</div>
            </div>

            <div>
                <div class="text-gray-500 text-sm mb-1">Deskripsi Lengkap</div>
                <div class="text-sm leading-relaxed">
                    {{ $paket->description ?? '-' }}
                </div>
            </div>

        </div>


        {{-- ================= RIGHT PANEL ================= --}}
        <div class="card space-y-5">

            {{-- Status --}}
            <div>
                <div class="text-sm text-gray-500 mb-2">Status</div>
                <div class="flex gap-2">
                    <span class="badge {{ $paket->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $paket->is_active ? 'Active' : 'Inactive' }}
                    </span>

                    <span class="badge {{ $paket->is_published ? 'badge-primary' : 'badge-warning' }}">
                        {{ $paket->is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>
            </div>

            {{-- Thumbnail --}}
            @if($paket->thumbnail)
                <div>
                    <div class="text-sm text-gray-500 mb-2">Thumbnail</div>
                    <img src="{{ asset('storage/'.$paket->thumbnail) }}"
                         class="w-full rounded-xl shadow">
                </div>
            @endif

        </div>

    </div>


    {{-- ================= HOTEL ================= --}}
    <div class="card">
        <h3 class="card-header">Hotel</h3>

        <div class="grid md:grid-cols-2 gap-4 text-sm">

            @foreach($paket->hotels as $hotel)
                <div class="border rounded-xl p-4 bg-gray-50">
                    <div class="font-medium">{{ $hotel->hotel_name }}</div>
                    <div class="text-gray-500">
                        {{ ucfirst($hotel->city) }} • ⭐ {{ $hotel->rating }}
                    </div>
                    <div class="text-gray-500 text-xs">
                        {{ $hotel->distance_to_haram }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>


    {{-- ================= ITINERARY ================= --}}
    <div class="card">
        <h3 class="card-header">Itinerary</h3>

        <div class="space-y-3 text-sm">

            @foreach($paket->itinerary as $item)
                <div class="flex justify-between border-b pb-2">
                    <div>
                        <span class="font-medium">
                            Hari {{ $item->day_order }}
                        </span>
                        — {{ $item->destination->city }}
                    </div>
                    <div class="text-gray-500">
                        {{ $item->note }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>


    {{-- ================= DEPARTURES ================= --}}
    <div class="card">
        <h3 class="card-header">Departures</h3>

        <div class="space-y-4 text-sm">

            @foreach($paket->departures as $dep)

                <div class="border rounded-xl p-4 bg-gray-50 space-y-2">

                    <div class="flex justify-between">
                        <div>
                            {{ \Carbon\Carbon::parse($dep->departure_date)->format('d M Y') }}
                            -
                            {{ \Carbon\Carbon::parse($dep->return_date)->format('d M Y') }}
                        </div>

                        <div class="font-medium">
                            Quota: {{ $dep->quota }}
                        </div>
                    </div>

                    <div class="flex gap-4 text-xs text-gray-600">
                        @foreach($dep->prices as $price)
                            <div>
                                {{ ucfirst($price->room_type) }} :
                                Rp {{ number_format($price->price,0,',','.') }}
                            </div>
                        @endforeach
                    </div>

                </div>

            @endforeach

        </div>
    </div>

</div>

@endsection