@extends('layouts.app')

@section('title','Manifest Detail')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold">
                Manifest - {{ $departure->paket->name }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ $departure->departure_date->format('d M Y') }}
            </p>
        </div>

        <div class="flex gap-2">

            {{-- Generate Seat --}}
            @php
                $hasSeat = $jamaahs->whereNotNull('seat_number')->count() > 0;
            @endphp

            @if(!$hasSeat)
                <form action="{{ route('manifests.generate.seat',$departure) }}" method="POST">
                    @csrf
                    <button class="btn btn-warning">
                        Generate Seat
                    </button>
                </form>
            @else
                <button class="btn btn-warning opacity-60 cursor-not-allowed" disabled>
                    Seat Sudah Digenerate
                </button>
            @endif

            {{-- Name Tag --}}
            <a href="{{ route('manifests.nametag.pdf', $departure) }}"
               target="_blank"
               class="btn btn-secondary">
                Cetak Name Tag
            </a>

            {{-- Export PDF --}}
            <a href="{{ route('manifests.export.pdf', $departure) }}"
               target="_blank"
               class="btn btn-primary">
                Export PDF
            </a>

        </div>

    </div>

    {{-- ================= FLASH MESSAGE ================= --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-100 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Seat</th>
                    <th>Cabang</th>
                    <th>Agent</th>
                    <th>Passport</th>
                </tr>
            </thead>

            <tbody>

            @forelse($jamaahs->sortBy('seat_number') as $jamaah)

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        <span class="font-medium">
                            {{ $jamaah->jamaah_code }}
                        </span>
                    </td>

                    <td>{{ $jamaah->nama_lengkap }}</td>

                    <td>
                        @if($jamaah->seat_number)
                            <span class="badge badge-primary">
                                {{ $jamaah->seat_number }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>

                    <td>{{ $jamaah->branch->name ?? '-' }}</td>

                    <td>{{ $jamaah->agent->name ?? '-' }}</td>

                    <td>{{ $jamaah->passport_number ?? '-' }}</td>
                </tr>

            @empty

                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-400">
                        Belum ada jamaah
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>

    </div>

</div>

@endsection