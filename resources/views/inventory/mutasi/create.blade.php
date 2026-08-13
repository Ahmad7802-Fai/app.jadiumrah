@extends('layouts.admin')

@section('title', 'Mutasi Barang')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Mutasi Barang</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.mutasi.index') }}"
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
                    Terdapat kesalahan pada input. Silakan periksa kembali data mutasi.
                </div>
            </div>
        @endif

        <form action="{{ route('inventory.mutasi.store') }}"
              method="POST"
              class="form">
            @csrf

            {{-- GRID 2 KOLOM --}}
            <div class="form-grid">

                {{-- BARANG --}}
                <div class="form-group">
                    <label>Barang</label>
                    <select name="item_id"
                            class="form-select @error('item_id') is-invalid @enderror"
                            required>
                        <option value="">Pilih Barang</option>
                        @foreach($items as $i)
                            <option value="{{ $i->id }}"
                                {{ old('item_id') == $i->id ? 'selected' : '' }}>
                                {{ $i->kode_barang }} — {{ $i->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- JUMLAH --}}
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number"
                           name="jumlah"
                           min="1"
                           value="{{ old('jumlah') }}"
                           class="form-control @error('jumlah') is-invalid @enderror"
                           required>
                    @error('jumlah')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TIPE --}}
                <div class="form-group">
                    <label>Tipe Mutasi</label>
                    <select name="tipe"
                            class="form-select @error('tipe') is-invalid @enderror"
                            required>
                        <option value="IN"  {{ old('tipe') === 'IN'  ? 'selected' : '' }}>
                            Barang Masuk
                        </option>
                        <option value="OUT" {{ old('tipe') === 'OUT' ? 'selected' : '' }}>
                            Barang Keluar
                        </option>
                    </select>
                    @error('tipe')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- KETERANGAN --}}
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan"
                          class="form-textarea"
                          rows="3"
                          placeholder="Tambahkan keterangan mutasi...">{{ old('keterangan') }}</textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="form-actions">
                <a href="{{ route('inventory.mutasi.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Mutasi
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
