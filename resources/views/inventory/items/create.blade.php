@extends('layouts.admin')

@section('title', 'Tambah Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Tambah Barang</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.items.index') }}" class="btn btn-light btn-sm">
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

        <form action="{{ route('inventory.items.store') }}"
              method="POST"
              class="form">
            @csrf

            {{-- GRID 2 KOLOM --}}
            <div class="form-grid">

                {{-- KODE BARANG --}}
                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text"
                           name="kode_barang"
                           class="form-control"
                           required>
                </div>

                {{-- NAMA BARANG --}}
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text"
                           name="nama_barang"
                           class="form-control"
                           required>
                </div>

                {{-- SATUAN --}}
                <div class="form-group">
                    <label>Satuan</label>
                    <input type="text"
                           name="satuan"
                           class="form-control">
                </div>

                {{-- KATEGORI --}}
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text"
                           name="kategori"
                           class="form-control">
                </div>

                {{-- HARGA BELI --}}
                <div class="form-group">
                    <label>Harga Beli</label>
                    <input type="number"
                           name="harga_beli"
                           class="form-control"
                           required>
                </div>

                {{-- HARGA JUAL --}}
                <div class="form-group">
                    <label>Harga Jual</label>
                    <input type="number"
                           name="harga_jual"
                           class="form-control"
                           required>
                </div>

            </div>

            {{-- DESKRIPSI (FULL WIDTH) --}}
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi"
                          class="form-textarea"
                          rows="3"></textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Barang
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
