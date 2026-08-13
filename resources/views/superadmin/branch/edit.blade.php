@extends('layouts.admin')

@section('title', 'Edit Cabang')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Cabang</h1>
            <p class="text-muted text-sm">
                Perbarui informasi cabang
            </p>
        </div>

        <a href="{{ route('superadmin.branch.index') }}"
           class="btn btn-outline-primary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ===============================
       ERROR SUMMARY
    ================================ --}}
    @if ($errors->any())
        <div class="card card-soft mb-3">
            <div class="card-body">
                <ul class="text-sm text-danger mb-0">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card card-hover" style="max-width:840px">

        <div class="card-header">
            <span class="card-title">Data Cabang</span>
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('superadmin.branch.update', $branch->id) }}"
                  class="form">
                @csrf
                @method('PUT')

                {{-- ===============================
                   INFORMASI CABANG
                ================================ --}}
                <div class="form-section">
                    <div class="form-title">Informasi Cabang</div>

                    <div class="form-grid">

                        {{-- KODE (READ ONLY) --}}
                        <div class="form-group">
                            <label>Kode Cabang</label>
                            <input type="text"
                                   value="{{ $branch->kode_cabang }}"
                                   class="form-control"
                                   readonly>

                            <div class="form-text">
                                Kode cabang dibuat otomatis dan tidak dapat diubah
                            </div>
                        </div>

                        {{-- NAMA --}}
                        <div class="form-group">
                            <label>Nama Cabang</label>
                            <input type="text"
                                   name="nama_cabang"
                                   value="{{ old('nama_cabang', $branch->nama_cabang) }}"
                                   class="form-control @error('nama_cabang') is-invalid @enderror"
                                   required>

                            @error('nama_cabang')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KOTA --}}
                        <div class="form-group">
                            <label>Kota</label>
                            <input type="text"
                                   name="kota"
                                   value="{{ old('kota', $branch->kota) }}"
                                   class="form-control @error('kota') is-invalid @enderror">

                            @error('kota')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ALAMAT --}}
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat"
                                      rows="3"
                                      class="form-textarea @error('alamat') is-invalid @enderror">{{ old('alamat', $branch->alamat) }}</textarea>

                            @error('alamat')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ===============================
                   STATUS INFO
                ================================ --}}
                <div class="form-section mt-3">
                    <div class="form-title">Status Cabang</div>

                    <div class="card card-soft">
                        <div class="card-body d-flex align-items-center gap-3">

                            <span class="badge {{ $branch->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
                            </span>

                            <div class="text-sm text-muted">
                                Status cabang diubah melalui toggle pada halaman
                                <strong>Master Cabang</strong>.
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ===============================
                   ACTIONS
                ================================ --}}
                <div class="form-actions">
                    <a href="{{ route('superadmin.branch.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update Cabang
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
