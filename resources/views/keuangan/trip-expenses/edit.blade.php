@extends('layouts.admin')

@section('title','Edit Biaya Keberangkatan')

@section('content')
<div class="page-container">

    {{-- ================= PAGE HEADER ================= --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Edit Biaya — {{ $paket->nama_paket }}
            </h1>
            <p class="page-subtitle">
                Perbarui data biaya keberangkatan umrah
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.trip.expenses.index', $paket->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

    </div>

    {{-- ================= FORM ================= --}}
    <div class="card">
        <div class="card-body">

            <form action="{{ route('keuangan.trip.expenses.update', [$paket->id, $item->id]) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="grid grid-cols-2 gap-4">

                @csrf
                @method('PUT')

                {{-- ===============================
                     KEBERANGKATAN
                =============================== --}}
                <div class="form-group col-span-2">
                    <label class="form-label">
                        Keberangkatan <span class="text-danger">*</span>
                    </label>

                    <select name="keberangkatan_id"
                            class="form-control @error('keberangkatan_id') is-invalid @enderror"
                            required>
                        <option value="">— Pilih Keberangkatan —</option>

                        @foreach($keberangkatanList as $k)
                            <option value="{{ $k->id }}"
                                {{ old('keberangkatan_id', $item->keberangkatan_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->kode_keberangkatan }}
                                • {{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }}
                            </option>
                        @endforeach
                    </select>

                    @error('keberangkatan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                     KATEGORI
                =============================== --}}
                <div class="form-group">
                    <label class="form-label">
                        Kategori Biaya <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="kategori"
                           class="form-control @error('kategori') is-invalid @enderror"
                           value="{{ old('kategori', $item->kategori) }}"
                           required>

                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                     JUMLAH
                =============================== --}}
                <div class="form-group">
                    <label class="form-label">
                        Jumlah (Rp) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="jumlah"
                           class="form-control @error('jumlah') is-invalid @enderror"
                           value="{{ old('jumlah', $item->jumlah) }}"
                           required>

                    @error('jumlah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                     TANGGAL
                =============================== --}}
                <div class="form-group">
                    <label class="form-label">
                        Tanggal Pengeluaran <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="tanggal"
                           class="form-control @error('tanggal') is-invalid @enderror"
                           value="{{ old('tanggal', $item->tanggal) }}"
                           required>

                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===============================
                     CATATAN
                =============================== --}}
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan"
                              rows="2"
                              class="form-control"
                              placeholder="Opsional">{{ old('catatan', $item->catatan) }}</textarea>
                </div>

                {{-- ===============================
                     FILE BUKTI
                =============================== --}}
                <div class="form-group col-span-2">
                    <label class="form-label">Upload Bukti</label>

                    @if($item->bukti)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$item->bukti) }}"
                               target="_blank"
                               class="text-sm text-primary">
                                <i class="fas fa-paperclip"></i>
                                Lihat bukti saat ini
                            </a>
                        </div>
                    @endif

                    <input type="file"
                           name="bukti"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.pdf">

                    <small class="form-hint">
                        Kosongkan jika tidak ingin mengganti • JPG, PNG, PDF • Maks 4 MB
                    </small>
                </div>

                {{-- ===============================
                     ACTIONS
                =============================== --}}
                <div class="col-span-2 flex justify-end gap-2">

                    <a href="{{ route('keuangan.trip.expenses.index', $paket->id) }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection
