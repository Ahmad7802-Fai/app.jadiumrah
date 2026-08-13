@extends('layouts.admin')

@section('title', 'Edit Distribusi')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Edit Distribusi</h1>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('inventory.distribusi.index') }}"
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
                    Terdapat kesalahan pada input. Silakan periksa kembali data distribusi.
                </div>
            </div>
        @endif

        <form action="{{ route('inventory.distribusi.update', $dist->id) }}"
              method="POST"
              class="form">
            @csrf
            @method('PUT')

            {{-- ================= MASTER DATA ================= --}}
            <div class="form-grid">

                <div class="form-group">
                    <label>Tanggal Distribusi</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ old('tanggal', $dist->tanggal?->format('Y-m-d')) }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Tujuan</label>
                    <input type="text"
                           name="tujuan"
                           value="{{ old('tujuan', $dist->tujuan) }}"
                           class="form-control"
                           required>
                </div>

            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan"
                          class="form-textarea"
                          rows="2"
                          placeholder="Tambahkan catatan (opsional)...">{{ old('catatan', $dist->catatan) }}</textarea>
            </div>

            {{-- ================= ITEMS ================= --}}
            <div class="form-section">
                <div class="form-title">Barang yang Didistribusikan</div>

                <div id="item-wrapper" class="form">

                    @php
                        $oldItems = old('item_id', []);
                    @endphp

                    @if(count($oldItems))
                        {{-- RENDER DARI OLD() --}}
                        @foreach($oldItems as $i => $oldItem)
                        <div class="form-grid item-row">

                            <div class="form-group">
                                <label>Barang</label>
                                <select name="item_id[]"
                                        class="form-select"
                                        required>
                                    <option value="">Pilih Barang</option>
                                    @foreach($items as $itm)
                                        <option value="{{ $itm->id }}"
                                            {{ $itm->id == $oldItem ? 'selected' : '' }}>
                                            {{ $itm->kode_barang }} — {{ $itm->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number"
                                       name="jumlah[]"
                                       min="1"
                                       value="{{ old('jumlah')[$i] ?? 1 }}"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="form-group form-group-inline">
                                <button type="button"
                                        class="btn btn-outline-danger btn-xs remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                        </div>
                        @endforeach
                    @else
                        {{-- RENDER DARI DATABASE --}}
                        @foreach($dist->items as $row)
                        <div class="form-grid item-row">

                            <div class="form-group">
                                <label>Barang</label>
                                <select name="item_id[]"
                                        class="form-select"
                                        required>
                                    @foreach($items as $itm)
                                        <option value="{{ $itm->id }}"
                                            {{ $itm->id == $row->item_id ? 'selected' : '' }}>
                                            {{ $itm->kode_barang }} — {{ $itm->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number"
                                       name="jumlah[]"
                                       min="1"
                                       value="{{ $row->jumlah }}"
                                       class="form-control"
                                       required>
                            </div>

                            <div class="form-group form-group-inline">
                                <button type="button"
                                        class="btn btn-outline-danger btn-xs remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                        </div>
                        @endforeach
                    @endif

                </div>

                <button type="button"
                        id="add-item"
                        class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Tambah Barang
                </button>
            </div>

            {{-- ================= ACTIONS ================= --}}
            <div class="form-actions">
                <a href="{{ route('inventory.distribusi.index') }}"
                   class="btn btn-light">
                    Batal
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Distribusi
                </button>
            </div>

        </form>

    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
document.getElementById('add-item').addEventListener('click', () => {
    const wrapper = document.getElementById('item-wrapper');
    const row = wrapper.querySelector('.item-row').cloneNode(true);

    row.querySelectorAll('input, select').forEach(el => el.value = '');
    wrapper.appendChild(row);
});

document.addEventListener('click', function(e){
    const btn = e.target.closest('.remove-row');
    if (!btn) return;

    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
    }
});
</script>

@endsection
