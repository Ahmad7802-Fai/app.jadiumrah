@extends('layouts.app')

@section('title','Data Paket')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-xl font-semibold text-gray-800">
                Data Paket
            </h1>

            <p class="text-sm text-gray-500">
                Kelola seluruh paket umrah
            </p>
        </div>

        @can('paket.create')
        <a href="{{ route('pakets.create') }}"
           class="btn btn-primary">
            + Buat Paket
        </a>
        @endcan

    </div>


    {{-- CARD --}}
    <div class="card overflow-hidden p-0">

        @if($pakets->count())

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                {{-- HEADER --}}
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 w-12 text-left">#</th>
                        <th class="px-4 py-3 text-left">Paket</th>
                        <th class="px-4 py-3 text-left">Harga</th>
                        <th class="px-4 py-3 text-left">Departure</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right w-56">Action</th>
                    </tr>
                </thead>


                {{-- BODY --}}
                <tbody class="divide-y bg-white">

                @foreach($pakets as $paket)

                @php
                    $dep = $paket->departures->first();
                @endphp

                <tr class="hover:bg-gray-50 transition">

                    {{-- NUMBER --}}
                    <td class="px-4 py-4 text-gray-400">
                        {{ $pakets->firstItem() + $loop->index }}
                    </td>


                    {{-- PAKET --}}
                    <td class="px-4 py-4">

                        <div class="font-medium text-gray-800">
                            {{ $paket->name }}
                        </div>

                        <div class="text-xs text-gray-400">
                            {{ $paket->code }}
                        </div>

                    </td>


                    {{-- PRICE --}}
                    <td class="px-4 py-4">

                        @if($paket->base_price)

                            <div class="text-xs text-gray-400">
                                Mulai dari
                            </div>

                            <div class="font-semibold text-primary">
                                Rp {{ number_format($paket->base_price,0,',','.') }}
                            </div>

                        @else

                            <span class="text-xs text-gray-400">
                                Belum ada harga
                            </span>

                        @endif

                    </td>


                    {{-- DEPARTURE --}}
                    <td class="px-4 py-4">

                        @if($dep)

                            <div class="font-medium text-gray-700">
                                {{ \Carbon\Carbon::parse($dep->departure_date)->format('d M Y') }}
                            </div>

                            <div class="text-xs text-gray-400">
                                {{ $dep->booked }} / {{ $dep->quota }} seat
                            </div>

                        @else

                            <span class="text-xs text-gray-400">
                                Belum ada departure
                            </span>

                        @endif

                    </td>


                    {{-- STATUS --}}
                    <td class="px-4 py-4">

                        <div class="flex gap-2 flex-wrap">

                            <span class="badge {{ $paket->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $paket->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            <span class="badge {{ $paket->is_published ? 'badge-primary' : 'badge-warning' }}">
                                {{ $paket->is_published ? 'Published' : 'Draft' }}
                            </span>

                        </div>

                    </td>


                    {{-- ACTION --}}
                    <td class="px-4 py-4">

                        <div class="flex justify-end gap-2">

                            <a href="{{ route('pakets.show',$paket) }}"
                               class="btn btn-outline btn-xs">
                                View
                            </a>

                            @can('paket.update')
                            <a href="{{ route('pakets.edit',$paket) }}"
                               class="btn btn-secondary btn-xs">
                                Edit
                            </a>
                            @endcan

                            @can('paket.delete')
                            <form action="{{ route('pakets.destroy',$paket) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus paket ini?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-xs">
                                    Delete
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
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $pakets->links() }}
        </div>


        @else

        <div class="py-20 text-center text-gray-400">

            <div class="text-lg font-medium">
                Belum ada paket
            </div>

            <p class="text-sm">
                Silakan buat paket baru terlebih dahulu.
            </p>

        </div>

        @endif

    </div>

</div>

@endsection