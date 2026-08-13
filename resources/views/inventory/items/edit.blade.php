@extends('layouts.admin')

@section('title', 'Edit Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Edit Barang</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.items.index') }}"
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

        <form action="{{ route('inventory.items.update', $item->id) }}"
              method="POST"
              class="form">
            @csrf
            @method('PUT')

            {{-- GRID 2 KOLOM --}}
            <div class="form-grid">

                {{-- KODE BARANG --}}
                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text"
                           name="kode_barang"
                           value="{{ old('kode_barang', $item->kode_barang) }}"
                           class="form-control"
                           required>
                </div>

                {{-- NAMA BARANG --}}
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text"
                           name="nama_barang"
                           value="{{ old('nama_barang', $item->nama_barang) }}"
                           class="form-control"
                           required>
                </div>

                {{-- SATUAN --}}
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text"
                           name="satuan"
                           value="{{ old('satuan', $item->satuan) }}"
                           class="form-control">
                </div>

                {{-- KATEGORI --}}
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text"
                           name="kategori"
                           value="{{ old('kategori', $item->kategori) }}"
                           class="form-control">
                </div>

                {{-- HARGA BELI --}}
                <div class="form-group">
                    <label>Harga Beli</label>
                    <input type="number"
                           name="harga_beli"
                           value="{{ old('harga_beli', $item->harga_beli) }}"
                           class="form-control"
                           min="0"
                           required>
                </div>

                {{-- HARGA JUAL --}}
                <div class="form-group">
                    <label>Harga Jual</label>
                    <input type="number"
                           name="harga_jual"
                           value="{{ old('harga_jual', $item->harga_jual) }}"
                           class="form-control"
                           min="0"
                           required>
                </div>

            </div>

            {{-- DESKRIPSI --}}
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi"
                          class="form-textarea"
                          rows="3">{{ old('deskripsi', $item->deskripsi) }}</textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('inventory.items.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
