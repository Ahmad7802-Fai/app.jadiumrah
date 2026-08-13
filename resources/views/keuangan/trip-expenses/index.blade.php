@extends('layouts.admin')

@section('title', 'Biaya Keberangkatan')

@section('content')
<div class="page-container page-wide">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Biaya Keberangkatan
                <span class="text-muted">— {{ $paket->nama_paket }}</span>
            </h1>
            <p class="page-subtitle">
                Monitoring dan pengelolaan biaya perjalanan umrah
            </p>
        </div>

        <div class="page-actions">

            {{-- BACK --}}
            <a href="{{ route('keuangan.biaya-keberangkatan.index') }}"
            class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            @if($data->count())
                <a href="{{ route('keuangan.trip.expenses.print.pdf', $paket->id) }}"
                class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </a>
            @endif

            <a href="{{ route('keuangan.trip.expenses.create', $paket->id) }}"
            class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Biaya
            </a>

        </div>

    </div>


    {{-- ================= SUMMARY ================= --}}
    <div class="stat-grid mb-3">

        <div class="card card-stat card-stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Jamaah</div>
                <div class="stat-value">
                    {{ number_format($totalJamaah) }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-danger">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value">
                    Rp {{ number_format($totalPengeluaran,0,',','.') }}
                </div>
            </div>
        </div>

        <div class="card card-stat card-stat-success">
            <div class="stat-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Biaya / Jamaah</div>
                <div class="stat-value">
                    Rp {{ $totalJamaah > 0
                        ? number_format($totalPengeluaran / $totalJamaah,0,',','.')
                        : 0 }}
                </div>
            </div>
        </div>

    </div>


    {{-- ================= TABLE ================= --}}
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
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $i => $row)
                        <tr>

                            <td>
                                {{ $data->firstItem() + $i }}
                            </td>

                            <td>
                                <strong>{{ $row->kategori }}</strong>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                            </td>

                            <td class="text-muted text-sm">
                                {{ $row->catatan ?: '—' }}
                            </td>

                            <td class="text-right fw-semibold">
                                Rp {{ number_format($row->jumlah,0,',','.') }}
                            </td>

                            <td class="text-right">

                                <a href="{{ route('keuangan.trip.expenses.edit', [$paket->id, $row->id]) }}"
                                   class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('keuangan.trip.expenses.destroy', [$paket->id, $row->id]) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus biaya ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Belum ada data biaya keberangkatan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- PAGINATION --}}
        <div class="card-footer">
            {{ $data->links() }}
        </div>

    </div>

</div>
@endsection
