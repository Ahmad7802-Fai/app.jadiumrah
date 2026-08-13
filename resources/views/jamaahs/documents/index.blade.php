@extends('layouts.app')

@section('title','Dokumen Jamaah')

@section('content')

<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div>
        <h1 class="text-2xl font-bold">Dokumen Jamaah</h1>
        <p class="text-sm text-gray-500">
            Monitoring seluruh dokumen jamaah
        </p>
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="card">

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <select name="document_type" class="input">
                <option value="">Semua Dokumen</option>
                <option value="passport">Passport</option>
                <option value="visa">Visa</option>
                <option value="ktp">KTP</option>
                <option value="kk">KK</option>
                <option value="vaccine">Vaccine</option>
            </select>

            <select name="branch_id" class="input">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>

            <select name="agent_id" class="input">
                <option value="">Semua Agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">
                        {{ $agent->nama }}
                    </option>
                @endforeach
            </select>

            <select name="expired" class="input">
                <option value="">Status Expired</option>
                <option value="yes">Expired</option>
            </select>

            <div class="md:col-span-4">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>

        </form>

    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card table-wrapper">

        <table class="table">
            <thead>
                <tr>
                    <th>Jamaah</th>
                    <th>Dokumen</th>
                    <th>Expired</th>
                    <th>Status</th>
                    <th>Cabang</th>
                    <th>Agent</th>
                </tr>
            </thead>
            <tbody>

            @forelse($documents as $doc)

                @php
                    $expired = $doc->expired_at && $doc->expired_at < now();
                    $soon = $doc->expired_at && $doc->expired_at->diffInDays(now()) < 90;
                @endphp

                <tr>

                    {{-- JAMAah --}}
                    <td>
                        @if($doc->jamaah)
                            <a href="{{ route('jamaah.show', $doc->jamaah_id) }}"
                               class="text-primary hover:underline">
                                {{ $doc->jamaah->nama_lengkap }}
                            </a>
                        @else
                            <span class="text-gray-400 italic">
                                Jamaah tidak ditemukan
                            </span>
                        @endif
                    </td>

                    {{-- DOKUMEN TYPE --}}
                    <td class="capitalize">
                        {{ $doc->document_type }}
                    </td>

                    {{-- EXPIRED DATE --}}
                    <td>
                        {{ $doc->expired_at
                            ? $doc->expired_at->format('d M Y')
                            : '-' }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        @if($expired)
                            <span class="badge badge-danger">
                                Expired
                            </span>
                        @elseif($soon)
                            <span class="badge badge-warning">
                                < 90 Hari
                            </span>
                        @else
                            <span class="badge badge-success">
                                Valid
                            </span>
                        @endif
                    </td>

                    {{-- CABANG --}}
                    <td>
                        {{ $doc->jamaah?->branch?->name ?? '-' }}
                    </td>

                    {{-- AGENT --}}
                    <td>
                        {{ $doc->jamaah?->agent?->nama ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-400">
                        Tidak ada data dokumen
                    </td>
                </tr>

            @endforelse

            </tbody>
        </table>

    </div>

    {{-- ================= PAGINATION ================= --}}
    <div>
        {{ $documents->withQueryString()->links() }}
    </div>

</div>

@endsection