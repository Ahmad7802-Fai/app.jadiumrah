@extends('layouts.admin')

@section('title', 'Stok Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Stok Barang</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.stok.create') }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Tambah
        </a>
    </div>
</div>

{{-- ===============================
   FAB MOBILE
================================ --}}
<a href="{{ route('inventory.stok.create') }}"
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
                        <th width="40" class="table-center">#</th>
                        <th>Barang</th>
                        <th class="table-center" width="80">Stok</th>
                        <th class="table-right col-actions">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($stok as $item)
                    <tr>

                        <td class="table-center">
                            {{ $loop->iteration }}
                        </td>

                        {{-- BARANG --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $item->barang->kode_barang }}
                            </div>
                            <div class="text-muted text-sm">
                                {{ $item->barang->nama_barang }}
                            </div>
                        </td>

                        {{-- STOK --}}
                        <td class="table-center">
                            <span class="badge badge-soft-success">
                                {{ $item->stok }}
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="table-right col-actions">
                            <div class="table-actions">

                                {{-- MUTASI --}}
                                <a href="{{ route('inventory.mutasi.index', ['item_id' => $item->id]) }}"
                                   class="btn btn-outline-primary btn-xs"
                                   title="Mutasi">
                                    <i class="fas fa-random"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('inventory.stok.edit', $item->id) }}"
                                   class="btn btn-outline-primary btn-xs"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- DELETE --}}
                                <button type="button"
                                        class="btn btn-outline-danger btn-xs js-delete"
                                        data-id="{{ $item->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="delete-form-{{ $item->id }}"
                                      action="{{ route('inventory.stok.destroy', $item->id) }}"
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
                        <td colspan="4" class="table-empty">
                            Belum ada stok barang
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>

@endsection
