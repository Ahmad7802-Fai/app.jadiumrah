@extends('layouts.admin')

@section('title', 'Master Barang')

@section('content')
<div class="page-wrapper page-inventory">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">
        <div class="page-header__title">
            <h1>Master Barang</h1>
        </div>

        <div class="page-header__actions">
            <a href="{{ route('inventory.items.create') }}"
               class="btn btn-primary btn-pill btn-sm">
                <i class="fas fa-plus"></i>
                <span>Tambah</span>
            </a>
        </div>
    </div>

    {{-- ================= CARD : TABLE ================= --}}
    <div class="card">

        <div class="table-wrapper">
            <table class="table table-compact">

    <thead>
        <tr>
            <th width="40">#</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th class="table-right">Harga Beli</th>
            <th class="table-right">Harga Jual</th>
            <th class="table-right col-actions">Aksi</th>
        </tr>
    </thead>

    <tbody>
    @forelse($items as $i => $item)
        <tr>

            <td>{{ $items->firstItem() + $i }}</td>

            {{-- KODE --}}
            <td class="fw-semibold">
                {{ $item->kode_barang }}
            </td>

            {{-- NAMA --}}
            <td>
                {{ $item->nama_barang }}
            </td>

            {{-- SATUAN --}}
            <td class="text-sm">
                {{ $item->satuan }}
            </td>

            {{-- HARGA BELI --}}
            <td class="table-right text-sm">
                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
            </td>

            {{-- HARGA JUAL --}}
            <td class="table-right text-sm">
                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
            </td>

            {{-- ACTION --}}
            <td class="table-right col-actions">
                <div class="table-actions">

                    <a href="{{ route('inventory.items.edit', $item->id) }}"
                       class="btn btn-outline-primary btn-xs">
                        <i class="fas fa-edit"></i>
                    </a>

                    <button type="button"
                            class="btn btn-outline-danger btn-xs js-delete"
                            data-id="{{ $item->id }}">
                        <i class="fas fa-trash"></i>
                    </button>

                    <form id="delete-form-{{ $item->id }}"
                          action="{{ route('inventory.items.destroy', $item->id) }}"
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
                Belum ada data barang
            </td>
        </tr>
    @endforelse
    </tbody>

</table>

        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="card-footer">
            {{ $items->links() }}
        </div>

    </div>
</div>
@endsection
