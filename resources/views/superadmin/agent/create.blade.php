@extends('layouts.admin')

@section('title', 'Tambah Agent')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Agent</h1>
            <p class="text-muted text-sm">
                Buat akun agent baru dan tentukan cabang
            </p>
        </div>

        <a href="{{ route('superadmin.agent.index', request()->only('branch_id')) }}"
           class="btn btn-outline-primary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- ===============================
       ERROR MESSAGE
    ================================ --}}
    @if ($errors->any())
        <div class="card card-soft mb-3">
            <div class="card-body">
                <ul class="text-danger text-sm mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <form action="{{ route('superadmin.agent.store') }}" method="POST">
        @csrf

        <div class="card card-hover max-w-3xl">

            {{-- ===============================
               DATA LOGIN
            ================================ --}}
            <div class="card-header">
                <span class="card-title">Data Login</span>
            </div>

            <div class="card-body form-grid">

                <div class="form-group">
                    <label>Nama Agent</label>
                    <input type="text"
                           name="nama"
                           value="{{ old('nama') }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Email (Login)</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

            </div>

            {{-- ===============================
               CABANG
            ================================ --}}
            <div class="card-header">
                <span class="card-title">Cabang</span>
            </div>

            <div class="card-body form-grid">

                <div class="form-group col-span-2">
                    <label>Pilih Cabang</label>
                    <select name="branch_id"
                            class="form-select"
                            required>
                        <option value="">— Pilih Cabang —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- ===============================
               DATA AGENT
            ================================ --}}
            <div class="card-header">
                <span class="card-title">Data Agent</span>
            </div>

            <div class="card-body form-grid">

                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone') }}"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label>Komisi (%)</label>
                    <input type="number"
                           step="0.01"
                           name="komisi_persen"
                           value="{{ old('komisi_persen', 0) }}"
                           class="form-control">
                    <div class="form-text">
                        Kode agent dibuat otomatis oleh sistem
                    </div>
                </div>

            </div>

            {{-- ===============================
               ACTION
            ================================ --}}
            <div class="card-footer">
                <div class="form-actions">
                    <a href="{{ route('superadmin.agent.index', request()->only('branch_id')) }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        💾 Simpan Agent
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
