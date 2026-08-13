@extends('layouts.app')

@section('title','Rooming')

@section('content')

@php
    $totalRooms      = $rooms->count();
    $totalAssigned   = $rooms->flatMap(fn($r) => $r->jamaahs)->count();
    $totalUnassigned = $unassigned->count();

    $maleCount = $rooms->flatMap(fn($r) => $r->jamaahs)
        ->filter(fn($j) => in_array(strtolower($j->gender),['male','l']))
        ->count();

    $femaleCount = $rooms->flatMap(fn($r) => $r->jamaahs)
        ->filter(fn($j) => in_array(strtolower($j->gender),['female','p']))
        ->count();
@endphp

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-start">

        <div>
            <h1 class="text-3xl font-bold">
                Rooming – {{ $departure->paket->name }}
            </h1>
            <p class="text-gray-500 mt-1">
                {{ $departure->departure_date->format('d M Y') }}
            </p>
        </div>

        <div class="flex gap-3">

            <form action="{{ route('rooming.clear',$departure) }}"
                  method="POST">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition">
                    Clear All
                </button>
            </form>

            <a href="{{ route('rooming.export.pdf',$departure) }}"
               target="_blank"
               class="px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">
                Export PDF
            </a>

        </div>
    </div>


    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="grid grid-cols-4 gap-6">

        <div class="bg-white p-5 rounded-2xl shadow border">
            <div class="text-sm text-gray-400">Total Rooms</div>
            <div class="text-2xl font-bold mt-2">
                {{ $totalRooms }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow border">
            <div class="text-sm text-gray-400">Assigned Jamaah</div>
            <div class="text-2xl font-bold mt-2 text-emerald-600">
                {{ $totalAssigned }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow border">
            <div class="text-sm text-gray-400">Unassigned</div>
            <div class="text-2xl font-bold mt-2 text-red-500">
                {{ $totalUnassigned }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow border">
            <div class="text-sm text-gray-400">Male / Female</div>
            <div class="text-xl font-bold mt-2">
                <span class="text-blue-600">{{ $maleCount }}</span>
                /
                <span class="text-pink-600">{{ $femaleCount }}</span>
            </div>
        </div>

    </div>


    {{-- ================= GENERATE SMART ================= --}}
    <div class="bg-white p-6 rounded-2xl shadow border">

        <form action="{{ route('rooming.generate',$departure) }}"
              method="POST"
              class="flex items-end gap-6">
            @csrf

            <div>
                <label class="text-xs text-gray-500">City</label>
                <select name="city" class="form-input mt-1">
                    <option value="makkah">Makkah</option>
                    <option value="madinah">Madinah</option>
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-500">Hotel (Optional)</label>
                <input type="text"
                       name="hotel"
                       class="form-input mt-1"
                       placeholder="Nama Hotel">
            </div>

            <div>
                <button class="px-6 py-2 bg-gray-900 text-white rounded-xl hover:bg-black transition">
                    Generate Smart Rooming
                </button>
            </div>

        </form>

    </div>


    {{-- ================= MAIN GRID ================= --}}
    <div class="grid grid-cols-4 gap-8">

        {{-- ================= UNASSIGNED ================= --}}
        <div class="col-span-1">

            <div class="bg-white p-5 rounded-2xl shadow border">

                <h2 class="font-semibold mb-4">
                    Belum Ada Kamar ({{ $totalUnassigned }})
                </h2>

                <div class="space-y-3 min-h-[350px]">

                    @forelse($unassigned as $jamaah)

                        <div class="p-3 bg-gray-50 rounded-xl border">

                            <div class="font-medium text-sm">
                                {{ $jamaah->nama_lengkap }}
                            </div>

                            <div class="text-xs text-gray-400 mt-1">
                                {{ strtoupper($jamaah->gender ?? '-') }}
                            </div>

                        </div>

                    @empty
                        <div class="text-sm text-gray-400">
                            Semua jamaah sudah memiliki kamar
                        </div>
                    @endforelse

                </div>

            </div>

        </div>


        {{-- ================= ROOMS ================= --}}
        <div class="col-span-3">

            <div class="grid grid-cols-2 gap-6">

                @forelse($rooms as $room)

                    @php
                        $filled = $room->jamaahs->count();
                        $percentage = $room->capacity > 0
                            ? ($filled / $room->capacity) * 100
                            : 0;
                    @endphp

                    <div class="bg-white p-6 rounded-2xl shadow-md border hover:shadow-lg transition">

                        {{-- HEADER --}}
                        <div class="flex justify-between mb-4">

                            <div>
                                <div class="text-xs text-gray-400 uppercase">
                                    {{ $room->city }}
                                </div>

                                <div class="text-lg font-semibold">
                                    Room {{ $room->room_number }}
                                </div>

                                @if($room->hotel_name)
                                    <div class="text-xs text-gray-500">
                                        {{ $room->hotel_name }}
                                    </div>
                                @endif
                            </div>

                            <div class="text-right">
                                <div class="text-sm font-semibold">
                                    {{ $filled }}/{{ $room->capacity }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Occupancy
                                </div>
                            </div>

                        </div>

                        {{-- PROGRESS --}}
                        <div class="w-full bg-gray-100 h-2 rounded-full mb-5">
                            <div class="h-2 rounded-full transition-all duration-500
                                {{ $percentage == 100 ? 'bg-red-500' : 'bg-emerald-500' }}"
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>

                        {{-- MEMBERS --}}
                        <div class="space-y-3 min-h-[150px]">

                            @foreach($room->jamaahs as $jamaah)

                                <div class="flex justify-between items-center bg-gray-50 px-4 py-3 rounded-xl border">

                                    <div>
                                        <div class="text-sm font-medium">
                                            {{ $jamaah->nama_lengkap }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $jamaah->jamaah_code ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="text-xs px-3 py-1 rounded-full
                                        {{ in_array(strtolower($jamaah->gender),['male','l'])
                                            ? 'bg-blue-100 text-blue-600'
                                            : 'bg-pink-100 text-pink-600' }}">
                                        {{ strtoupper($jamaah->gender ?? '-') }}
                                    </div>

                                </div>

                            @endforeach

                        </div>

                        {{-- DELETE --}}
                        <div class="mt-6 pt-4 border-t">
                            <form action="{{ route('rooming.destroy',$room) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-500 hover:text-red-600">
                                    Hapus Room
                                </button>
                            </form>
                        </div>

                    </div>

                @empty

                    <div class="col-span-2 text-center text-gray-400 py-20">
                        Belum ada kamar digenerate
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection