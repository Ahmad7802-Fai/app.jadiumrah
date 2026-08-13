@extends('layouts.admin')

@section('title', 'Edit Stok')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Edit Stok</h1>
        <div class="page-subtitle">
            Master Barang — Update stok item
        </div>
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

        {{-- ERROR SUMMARY --}}
        @if ($errors->any())
            <div class="form-section">
                <div class="form-error">
                    Terdapat kesalahan pada input. Silakan periksa kembali.
                </div>
            </div>
        @endif

        <form action="{{ route('inventory.stok.update', $stok->id) }}"
              method="POST"
              class="form">
            @csrf
            @method('PUT')

            {{-- GRID 2 KOLOM --}}
            <div class="form-grid">

                {{-- ITEM (READ ONLY) --}}
                <div class="form-group">
                    <label>Barang</label>
                    <select class="form-select" disabled>
                        <option>
                            {{ $stok->item?->kode_barang }} — {{ $stok->item?->nama_barang }}
                        </option>
                    </select>
                    <input type="hidden" name="item_id" value="{{ $stok->item_id }}">
                    <div class="form-hint">
                        Nama barang tidak dapat diubah. Untuk pindah item, lakukan mutasi stok.
                    </div>
                </div>

                {{-- STOK --}}
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number"
                           name="stok"
                           min="0"
                           step="1"
                           value="{{ old('stok', $stok->stok) }}"
                           class="form-control @error('stok') is-invalid @enderror"
                           required>
                    @error('stok')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SUMBER (OPTIONAL) --}}
                <div class="form-group">
                    <label>Sumber</label>
                    <input type="text"
                           name="sumber"
                           value="{{ old('sumber', $stok->sumber) }}"
                           class="form-control"
                           placeholder="Contoh: Penambahan gudang">
                    <div class="form-hint">
                        Catatan singkat atau referensi sumber stok.
                    </div>
                </div>

            </div>

            {{-- KETERANGAN --}}
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"
                          class="form-textarea"
                          rows="3">{{ old('keterangan', $stok->keterangan) }}</textarea>
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
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
