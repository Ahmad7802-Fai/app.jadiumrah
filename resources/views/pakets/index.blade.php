@extends('layouts.app')

@section('title','Data Paket')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-5 space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Data Paket
            </h1>
            <p class="text-xs text-gray-500">
                Kelola paket umrah
            </p>
        </div>

        @can('paket.create')
        <a href="{{ route('pakets.create') }}"
           class="btn btn-primary btn-xs">
            + Paket
        </a>
        @endcan

    </div>


    {{-- CARD TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        @if($pakets->count())

        <div class="overflow-x-auto">

            <table class="min-w-full text-xs">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wide text-[10px]">
                    <tr>
                        <th class="px-3 py-2 w-10 text-left">#</th>
                        <th class="px-3 py-2 text-left">Paket</th>
                        <th class="px-3 py-2 text-left">Harga</th>
                        <th class="px-3 py-2 text-left">Departure</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-right w-40">Action</th>
                    </tr>
                </thead>


                {{-- BODY --}}
                <tbody class="divide-y">

                @foreach($pakets as $paket)

                @php
                    $dep = $paket->departures->first();
                @endphp

                <tr class="hover:bg-gray-50 transition">

                    {{-- NO --}}
                    <td class="px-3 py-3 text-gray-400">
                        {{ $pakets->firstItem() + $loop->index }}
                    </td>

                    {{-- PAKET --}}
                    <td class="px-3 py-3">

                        <div class="font-medium text-gray-800 text-sm leading-tight">
                            {{ $paket->name }}
                        </div>

                        <div class="text-[10px] text-gray-400">
                            {{ $paket->code }}
                        </div>

                    </td>

                    {{-- PRICE --}}
                    <td class="px-3 py-3">

                        @if($paket->base_price)

                            <div class="text-[10px] text-gray-400">
                                mulai
                            </div>

                            <div class="font-semibold text-primary text-sm">
                                Rp {{ number_format($paket->base_price,0,',','.') }}
                            </div>

                        @else

                            <span class="text-[10px] text-gray-400">
                                -
                            </span>

                        @endif

                    </td>

                    {{-- DEPARTURE --}}
                    <td class="px-3 py-3">

                        @if($dep)

                            <div class="text-sm font-medium text-gray-700">
                                {{ \Carbon\Carbon::parse($dep->departure_date)->format('d M Y') }}
                            </div>

                            <div class="text-[10px] text-gray-400">
                                {{ $dep->booked }}/{{ $dep->quota }}
                            </div>

                        @else

                            <span class="text-[10px] text-gray-400">
                                -
                            </span>

                        @endif

                    </td>

                    {{-- STATUS --}}
                    <td class="px-3 py-3">

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

                    </td>

                    {{-- ACTION --}}
                    <td class="px-3 py-3">

                        <div class="flex justify-end gap-1">

                            <a href="{{ route('pakets.show',$paket) }}"
                               class="px-2 py-1 text-[10px] border rounded hover:bg-gray-50">
                                View
                            </a>

                            @can('paket.update')
                            <a href="{{ route('pakets.edit',$paket) }}"
                               class="px-2 py-1 text-[10px] bg-gray-100 rounded hover:bg-gray-200">
                                Edit
                            </a>
                            @endcan

                            @can('paket.delete')
                            <form action="{{ route('pakets.destroy',$paket) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus paket ini?')">

                                @csrf
                                @method('DELETE')

                                <button class="px-2 py-1 text-[10px] bg-red-500 text-white rounded hover:bg-red-600">
                                    Del
                                </button>

                            </form>
                            @endcan

                        </div>

                    </td>

                </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        <div class="px-4 py-3 border-t bg-gray-50 text-xs">
            {{ $pakets->links() }}
        </div>

        @else

        <div class="py-16 text-center text-gray-400 text-sm">
            Belum ada paket
        </div>

        @endif

    </div>

</div>

@endsection