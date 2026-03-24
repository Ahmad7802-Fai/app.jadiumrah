@extends('layouts.app')

@section('title','Keberangkatan')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Keberangkatan
            </h1>
            <p class="text-sm text-gray-500">
                Kelola seluruh jadwal keberangkatan paket
            </p>
        </div>

        @can('departure.create')
        <a href="{{ route('departures.create') }}"
           class="btn btn-primary">
            + Tambah Keberangkatan
        </a>
        @endcan
    </div>


    {{-- ================= FILTER ================= --}}
    <div class="card">

        <form method="GET" class="grid md:grid-cols-3 gap-4">

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama / kode paket..."
                   class="input">

            <select name="status" class="input">
                <option value="">Semua Status</option>
                <option value="open"
                    {{ request('status')=='open'?'selected':'' }}>
                    Open
                </option>
                <option value="closed"
                    {{ request('status')=='closed'?'selected':'' }}>
                    Closed
                </option>
            </select>

            <button class="btn btn-primary">
                Filter
            </button>

        </form>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Paket</th>
                    <th>Keberangkatan</th>
                    <th>Quota</th>
                    <th>Status</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

           <tbody>
                @forelse($departures as $departure)

                @php
                    $remaining = $departure->remainingQuota();
                    $percentage = $departure->quota > 0 
                        ? round(($departure->booked / $departure->quota) * 100) 
                        : 0;

                    $isFull = $remaining <= 0;
                @endphp

                <tr>
                    <td>{{ $departure->departure_code ?? '-' }}</td>

                    <td>
                        <div class="font-medium">
                            {{ $departure->paket->name }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $departure->paket->code }}
                        </div>
                    </td>

                    <td>
                        {{ $departure->departure_date->format('d M Y') }}
                    </td>

                    <td>
                        {{-- QUOTA TEXT --}}
                        <div class="text-sm mb-1">
                            {{ $departure->booked }} / {{ $departure->quota }}
                        </div>

                        {{-- PROGRESS BAR --}}
                        <div class="w-40 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full
                                {{ $isFull ? 'bg-red-600' : 'bg-green-600' }}"
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            {{ $remaining }} seat tersisa
                        </div>
                    </td>

                    <td>
                        @if($departure->remainingQuota() <= 0)
                            <span class="badge badge-danger">
                                FULL
                            </span>

                        @elseif($departure->is_closed)
                            <span class="badge badge-secondary">
                                CLOSED
                            </span>

                        @else
                            <span class="badge badge-success">
                                OPEN
                            </span>
                        @endif
                    </td>

                    <td class="flex gap-2">
                        <a href="{{ route('departures.edit', $departure) }}"
                        class="btn btn-sm btn-secondary">
                            Edit
                        </a>

                        <form action="{{ route('departures.destroy', $departure) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin hapus departure ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        Belum ada data departure
                    </td>
                </tr>
                @endforelse
                </tbody>

        </table>

    </div>


    {{-- ================= PAGINATION ================= --}}
    <div>
        {{ $departures->withQueryString()->links() }}
    </div>

</div>

@endsection