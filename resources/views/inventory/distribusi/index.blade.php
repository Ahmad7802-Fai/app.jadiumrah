@extends('layouts.admin')

@section('title', 'Distribusi Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Distribusi Barang</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.distribusi.create') }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Distribusi Baru
        </a>
    </div>
</div>

{{-- ===============================
   FAB MOBILE
================================ --}}
<a href="{{ route('inventory.distribusi.create') }}"
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
                        <th>Tanggal</th>
                        <th>Tujuan</th>
                        <th class="d-none d-md-table-cell">Item</th>
                        <th class="table-right col-actions">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($list as $row)
                    <tr>

                        <td class="table-center">
                            {{ $loop->iteration }}
                        </td>

                        {{-- TANGGAL --}}
                        <td class="fw-semibold">
                            {{ $row->tanggal?->format('d M Y') }}

                            {{-- MOBILE ITEM COUNT --}}
                            <div class="d-md-none text-sm text-success">
                                {{ $row->items->count() }} item
                            </div>
                        </td>

                        {{-- TUJUAN --}}
                        <td class="fw-semibold">
                            {{ $row->tujuan }}
                        </td>

                        {{-- ITEM COUNT (DESKTOP) --}}
                        <td class="d-none d-md-table-cell">
                            <span class="badge badge-soft-success">
                                {{ $row->items->count() }} item
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="table-right col-actions">
                            <div class="table-actions">

                                <a href="{{ route('inventory.distribusi.edit', $row->id) }}"
                                   class="btn btn-outline-primary btn-xs"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button"
                                        class="btn btn-outline-danger btn-xs js-delete"
                                        data-id="{{ $row->id }}"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="delete-form-{{ $row->id }}"
                                      action="{{ route('inventory.distribusi.destroy', $row->id) }}"
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
                        <td colspan="5" class="table-empty">
                            Belum ada distribusi barang
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>

@endsection
