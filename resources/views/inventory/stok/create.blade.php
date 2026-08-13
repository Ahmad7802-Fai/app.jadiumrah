@extends('layouts.admin')

@section('title', 'Tambah Stok')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Tambah Stok</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.stok.index') }}"
           class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

{{-- ===============================
   FORM CARD
================================ --}}
<div class="card card-hover">
    <div class="card-body">

        <form action="{{ route('inventory.stok.store') }}"
              method="POST"
              class="form">
            @csrf

            {{-- GRID 2 KOLOM --}}
            <div class="form-grid">

                {{-- BARANG --}}
                <div class="form-group">
                    <label>Barang</label>
                    <select name="item_id"
                            class="form-select"
                            required>
                        <option value="">Pilih Barang</option>
                        @foreach($items as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->kode_barang }} — {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- JUMLAH --}}
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number"
                           name="stok"
                           class="form-control"
                           min="1"
                           required>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('inventory.stok.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
