@extends('layouts.app')

@section('title','Rooming List')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold">Rooming List</h1>
        <p class="text-sm text-gray-500">
            Pilih keberangkatan untuk mengatur pembagian kamar
        </p>
    </div>

    {{-- TABLE --}}
    <div class="card table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Paket</th>
                    <th>Tanggal</th>
                    <th>Total Jamaah</th>
                    <th>Total Kamar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            @forelse($departures as $departure)
                <tr>
                    <td>{{ $departure->paket->name }}</td>
                    <td>{{ $departure->departure_date->format('d M Y') }}</td>
                    <td>
                        {{ $departure->bookings->flatMap->jamaahs->unique('id')->count() }}
                    </td>
                    <td>
                        {{ $departure->rooms->count() }}
                    </td>
                    <td>
                        <a href="{{ route('rooming.show',$departure) }}"
                           class="btn btn-primary btn-sm">
                            Kelola Rooming
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        Belum ada departure
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{ $departures->links() }}

</div>

@endsection