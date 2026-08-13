@extends('layouts.app')

@section('title','Manifest')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Manifest</h1>
        <p class="text-sm text-gray-500">
            Pilih keberangkatan untuk melihat daftar jamaah
        </p>
    </div>

    <div class="card table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Paket</th>
                    <th>Tanggal Berangkat</th>
                    <th>Quota</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            @forelse($departures as $departure)
                <tr>
                    <td>{{ $departure->paket->name }}</td>
                    <td>{{ $departure->departure_date->format('d M Y') }}</td>
                    <td>{{ $departure->booked }} / {{ $departure->quota }}</td>
                    <td>
                        <a href="{{ route('manifests.show',$departure) }}"
                           class="btn btn-primary">
                            Lihat Manifest
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">
                        Tidak ada keberangkatan
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    {{ $departures->links() }}

</div>

@endsection