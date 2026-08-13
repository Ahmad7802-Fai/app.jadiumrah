@extends('layouts.admin')

@section('title', 'Log Mutasi Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Log Mutasi Barang</h1>
    </div>

    <div class="page-action">
        <a href="{{ route('inventory.mutasi.create') }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Mutasi Baru
        </a>
    </div>
</div>

{{-- ===============================
   FAB MOBILE
================================ --}}
<a href="{{ route('inventory.mutasi.create') }}"
   class="fab d-md-none">
    <i class="fas fa-plus"></i>
</a>

{{-- ===============================
   TABLE
================================ --}}
<div class="card card-hover">
    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-compact">

                <thead>
                    <tr>
                        <th width="50" class="table-center">#</th>
                        <th>Barang</th>
                        <th class="table-center" width="70">Qty</th>
                        <th class="table-center" width="90">Tipe</th>
                        <th class="d-none d-md-table-cell">Keterangan</th>
                        <th class="table-center" width="140">Tanggal</th>
                        <th class="table-right col-actions">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($mutasi as $row)
                    <tr>

                        <td class="table-center">
                            {{ $loop->iteration }}
                        </td>

                        {{-- BARANG --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $row->barang->kode_barang }}
                            </div>
                            <div class="text-muted text-sm">
                                {{ $row->barang->nama_barang }}
                            </div>
                        </td>

                        {{-- QTY --}}
                        <td class="table-center">
                            <span class="badge {{ $row->jumlah > 0 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                {{ $row->jumlah }}
                            </span>
                        </td>

                        {{-- TIPE --}}
                        <td class="table-center">
                            <span class="badge {{ $row->tipe === 'IN' ? 'badge-soft-primary' : 'badge-soft-dark' }}">
                                {{ $row->tipe === 'IN' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>

                        {{-- KETERANGAN --}}
                        <td class="d-none d-md-table-cell text-sm text-muted">
                            {{ $row->keterangan ?? '-' }}
                        </td>

                        {{-- TANGGAL --}}
                        <td class="table-center text-sm">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="table-right col-actions">
                            <div class="table-actions">

                                <a href="{{ route('inventory.mutasi.edit', $row->id) }}"
                                   class="btn btn-outline-primary btn-xs"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button"
                                        class="btn btn-outline-danger btn-xs js-delete"
                                        data-id="{{ $row->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="delete-form-{{ $row->id }}"
                                      action="{{ route('inventory.mutasi.destroy', $row->id) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-empty">
                            Belum ada log mutasi
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>

@endsection
