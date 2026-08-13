@extends('layouts.admin')

@section('title','Biaya Keberangkatan')

@section('content')
<div class="page-container page-wide">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">Biaya Keberangkatan</h1>
            <p class="page-subtitle">
                Monitoring biaya dan jumlah jamaah setiap keberangkatan
            </p>
        </div>
    </div>

    {{-- =====================================================
    | TABLE CARD
    ===================================================== --}}
    <div class="card">

        <div class="card-body p-0">

            <div class="table-wrap">
                <table class="table table-compact table-premium mb-0">

                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Paket</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th class="table-right">Jamaah</th>
                            <th class="table-right">Total Biaya</th>
                            <th>Status</th>
                            <th class="col-actions table-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $i => $row)

                        <tr>

                            {{-- NO --}}
                            <td data-label="#">
                                {{ $data->firstItem() + $i }}
                            </td>

                            {{-- PAKET --}}
                            <td data-label="Paket">
                                <div class="fw-semibold">
                                    {{ $row->paket->nama_paket ?? '-' }}
                                </div>
                                <div class="text-muted text-xs">
                                    {{ $row->paket->hotel_mekkah ?? '-' }}
                                    –
                                    {{ $row->paket->hotel_madinah ?? '-' }}
                                </div>
                            </td>

                            {{-- KODE --}}
                            <td data-label="Kode">
                                {{ $row->kode_keberangkatan }}
                            </td>

                            {{-- TANGGAL --}}
                            <td data-label="Tanggal">
                                {{ $row->tanggal_berangkat?->format('d M Y') ?? '-' }}
                            </td>

                            {{-- JAMAAH --}}
                            <td data-label="Jamaah" class="table-right">
                                <strong>{{ $row->total_jamaah }}</strong>
                                <span class="text-muted text-xs">
                                    / {{ $row->kuota ?? '∞' }}
                                </span>
                            </td>

                            {{-- TOTAL BIAYA --}}
                            <td data-label="Total Biaya" class="table-right">
                                <strong class="text-danger">
                                    Rp {{ number_format($row->total_biaya ?? 0, 0, ',', '.') }}
                                </strong>
                            </td>

                            {{-- STATUS --}}
                            <td data-label="Status">
                                @php($status = $row->status_label)
                                <span class="badge
                                    @if($status === 'Selesai') badge-soft-success
                                    @elseif($status === 'Sudah Berangkat') badge-soft-info
                                    @else badge-soft-secondary
                                    @endif">
                                    {{ $status }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td data-label="Aksi" class="table-right">
                                <div class="table-action">
                                    <a href="{{ route('keuangan.trip.expenses.index', $row->id_paket_master) }}"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-money-bill"></i>
                                        <span class="d-none d-md-inline">Biaya</span>
                                    </a>
                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="table-empty">
                                Belum ada data keberangkatan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            {{ $data->links() }}
        </div>

    </div>

</div>
@endsection
