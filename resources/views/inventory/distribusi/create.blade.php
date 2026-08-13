@extends('layouts.admin')

@section('title', 'Distribusi Baru')

@section('content')

{{-- ===============================
   PAGE HEADER
================================ --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Distribusi Barang</h1>
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

        <form action="{{ route('inventory.distribusi.store') }}"
              method="POST"
              class="form">
            @csrf

            {{-- ================= MASTER DATA ================= --}}
            <div class="form-grid">

                <div class="form-group">
                    <label>Tanggal Distribusi</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Tujuan</label>
                    <input type="text"
                           name="tujuan"
                           value="{{ old('tujuan') }}"
                           placeholder="Contoh: Kantor Cabang Bandung"
                           class="form-control"
                           required>
                </div>

            </div>

            <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan"
                          class="form-textarea"
                          rows="2"
                          placeholder="Tambahkan catatan (opsional)...">{{ old('catatan') }}</textarea>
            </div>

            {{-- ================= ITEMS ================= --}}
            <div class="form-section">
                <div class="form-title">Barang yang Didistribusikan</div>

                <div id="item-wrapper" class="form">

                    <div class="form-grid item-row">

                        {{-- BARANG --}}
                        <div class="form-group">
                            <label>Barang</label>
                            <select name="item_id[]"
                                    class="form-select"
                                    required>
                                <option value="">Pilih Barang</option>
                                @foreach($items as $i)
                                    <option value="{{ $i->id }}">
                                        {{ $i->kode_barang }} — {{ $i->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- JUMLAH --}}
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number"
                                   name="jumlah[]"
                                   min="1"
                                   class="form-control"
                                   required>
                        </div>

                        {{-- REMOVE --}}
                        <div class="form-group form-group-inline remove-col">
                            <button type="button"
                                    class="btn btn-outline-danger btn-xs remove-row"
                                    style="display:none">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                    </div>

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
                    Simpan Distribusi
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

    const removeBtn = row.querySelector('.remove-row');
    removeBtn.style.display = 'inline-flex';

    wrapper.appendChild(row);
});

document.addEventListener('click', function(e){
    const btn = e.target.closest('.remove-row');
    if (btn) {
        btn.closest('.item-row').remove();
    }
});
</script>

@endsection
