@extends('layouts.admin')

@section('title', 'Tambah Cabang')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Cabang</h1>
            <p class="text-muted text-sm">
                Tambahkan cabang baru beserta admin cabangnya
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
                  action="{{ route('superadmin.branch.store') }}"
                  class="form">
                @csrf

                {{-- ===============================
                   DATA CABANG
                ================================ --}}
                <div class="form-section">
                    <div class="form-title">Informasi Cabang</div>

                    <div class="form-grid">

                        {{-- PREFIX --}}
                        <div class="form-group">
                            <label>Prefix Cabang</label>
                            <input type="text"
                                   name="prefix"
                                   value="{{ old('prefix') }}"
                                   class="form-control text-uppercase @error('prefix') is-invalid @enderror"
                                   placeholder="LMP / JKT / BDG"
                                   maxlength="5"
                                   pattern="[A-Za-z]{2,5}"
                                   required>

                            <div class="form-text">
                                Huruf saja. Sistem akan membuat kode otomatis
                                <strong>LMP-01</strong>, <strong>LMP-02</strong>, dst.
                            </div>

                            @error('prefix')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NAMA --}}
                        <div class="form-group">
                            <label>Nama Cabang</label>
                            <input type="text"
                                   name="nama_cabang"
                                   value="{{ old('nama_cabang') }}"
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
                                   value="{{ old('kota') }}"
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
                                      class="form-textarea @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>

                            @error('alamat')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ===============================
                   ADMIN CABANG
                ================================ --}}
                <div class="form-section mt-3">
                    <div class="form-title">Admin Cabang (Auto User)</div>

                    <div class="form-grid">

                        {{-- EMAIL ADMIN --}}
                        <div class="form-group">
                            <label>Email Admin</label>
                            <input type="email"
                                   name="admin_email"
                                   value="{{ old('admin_email') }}"
                                   class="form-control @error('admin_email') is-invalid @enderror"
                                   required>

                            @error('admin_email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PASSWORD ADMIN --}}
                        <div class="form-group">
                            <label>Password Admin</label>
                            <input type="password"
                                   name="admin_password"
                                   class="form-control @error('admin_password') is-invalid @enderror"
                                   required>

                            <div class="form-text">
                                Digunakan untuk login admin cabang
                            </div>

                            @error('admin_password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
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
                        Simpan Cabang
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
