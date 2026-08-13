@extends('layouts.admin')

@section('title', 'Detail Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Layanan</h1>
            <p class="page-subtitle">
                Informasi layanan dan daftar item
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.layanan.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>

            <a href="{{ route('keuangan.layanan.items.create', $layanan->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>
                Tambah Item
            </a>
        </div>
    </div>


    {{-- =====================================================
    | DETAIL CARD
    ===================================================== --}}
    <div class="card mb-4 card-hover">
        <div class="card-body card-body-lg">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="text-muted small">Kode Layanan</div>
                    <div class="fw-semibold">
                        {{ $layanan->kode_layanan }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Kategori</div>
                    <div class="fw-semibold">
                        {{ strtoupper($layanan->kategori) }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Status</div>
                    <div class="fw-semibold">
                        {{ $layanan->status ? 'Aktif' : 'Nonaktif' }}
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small">Deskripsi</div>
                    <div>
                        {!! $layanan->deskripsi ?: '<span class="text-muted">Tidak ada deskripsi.</span>' !!}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- =====================================================
    | ITEM TABLE
    ===================================================== --}}
    <div class="card card-hover">
        <div class="card-header">
            <h3 class="card-title">Item Layanan</h3>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact">

                    <thead>
                        <tr>
                            <th>Nama Item</th>
                            <th>Harga</th>
                            <th>Satuan</th>
                            <th>Vendor</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="col-actions text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($layanan->items as $item)
                        <tr>

                            <td data-label="Nama Item">
                                <strong>{{ $item->nama_item }}</strong>
                            </td>

                            <td data-label="Harga">
                                Rp {{ number_format($item->harga,0,',','.') }}
                            </td>

                            <td data-label="Satuan">
                                {{ $item->satuan }}
                            </td>

                            <td data-label="Vendor">
                                {{ $item->vendor ?: '-' }}
                            </td>

                            <td data-label="Tanggal">
                                @if($item->tanggal_mulai)
                                    {{ date('d M Y', strtotime($item->tanggal_mulai)) }}
                                    –
                                    {{ date('d M Y', strtotime($item->tanggal_selesai)) }}
                                @else
                                    -
                                @endif
                            </td>

                            <td data-label="Status">
                                {{ $item->status ? 'Aktif' : 'Nonaktif' }}
                            </td>

                            <td class="col-actions" data-label="Aksi">
                                <div class="table-actions">

                                    <a href="{{ route('keuangan.layanan.items.edit', $item->id) }}"
                                       class="btn btn-outline-secondary btn-sm"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('keuangan.layanan.items.status', $item->id) }}"
                                          method="POST">
                                        @csrf
                                        <button class="btn btn-outline-warning btn-sm"
                                                title="Toggle Status">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="table-empty">
                                Belum ada item layanan.
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
