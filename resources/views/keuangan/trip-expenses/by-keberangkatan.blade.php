@extends('layouts.admin')

@section('title', 'Detail Biaya Keberangkatan')

@section('content')
<div class="page-container page-wide">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Detail Biaya Keberangkatan
            </h1>
            <p class="page-subtitle">
                {{ $keberangkatan->kode_keberangkatan }} — {{ $keberangkatan->paket?->nama_paket }}
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ url()->previous() }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <a href="{{ route('keuangan.trip.expenses.create', $keberangkatan->id_paket_master) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Biaya
            </a>
        </div>
    </div>

    {{-- ================= INFO TRIP ================= --}}
    <div class="stat-grid mb-3">

        <div class="card card-stat card-stat-primary">
            <div class="stat-content">
                <div class="stat-label">Tanggal Berangkat</div>
                <div class="stat-value">
                    {{ \Carbon\Carbon::parse($keberangkatan->tanggal_berangkat)->format('d M Y') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-info">
            <div class="stat-content">
                <div class="stat-label">Jumlah Jamaah</div>
                <div class="stat-value">
                    {{ number_format($keberangkatan->jamaah()->count()) }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-content">
                <div class="stat-label">Total Biaya Trip</div>
                <div class="stat-value">
                    Rp {{ number_format($totalBiaya,0,',','.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- ================= TABLE BIAYA ================= --}}
    <div class="card card-hover">

        <div class="card-body p-0">

            <div class="table-wrapper">
                <table class="table table-compact align-middle">

                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Catatan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($expenses as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td class="fw-semibold">
                                {{ $row->kategori }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                            </td>

                            <td class="text-muted">
                                {{ $row->catatan ?: '—' }}
                            </td>

                            <td class="text-end fw-semibold text-danger">
                                Rp {{ number_format($row->jumlah,0,',','.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada biaya untuk keberangkatan ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>

</div>
@endsection
