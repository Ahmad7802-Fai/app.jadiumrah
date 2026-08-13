@extends('layouts.admin')

@section('title','Master Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Layanan</h1>
            <p class="page-subtitle">
                Manajemen daftar layanan yang tersedia
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.layanan.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>
                Tambah Layanan
            </a>
        </div>
    </div>


    {{-- =====================================================
    | TABLE CARD
    ===================================================== --}}
    <div class="card card-hover">

        <div class="card-header">
            <h3 class="card-title">Daftar Layanan</h3>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact">

                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Layanan</th>
                            <th>Kategori</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th class="col-actions text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($layanan as $row)
                        <tr>
                            <td data-label="Kode">
                                <strong>{{ $row->kode_layanan }}</strong>
                            </td>

                            <td data-label="Nama">
                                {{ $row->nama_layanan }}
                            </td>

                            <td data-label="Kategori">
                                {{ strtoupper($row->kategori) }}
                            </td>

                            <td data-label="Item">
                                {{ $row->items_count }} item
                            </td>

                            <td data-label="Status">
                                @if($row->status)
                                    <span class="badge badge-soft-success fw-semibold">Aktif</span>
                                @else
                                    <span class="text-danger fw-semibold">Nonaktif</span>
                                @endif
                            </td>

                            <td class="col-actions text-right">
                                <div class="table-actions">
                                    <a href="{{ route('keuangan.layanan.show',$row->id) }}"
                                       class="btn btn-outline-secondary btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Belum ada data layanan.
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
