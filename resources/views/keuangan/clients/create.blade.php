@extends('layouts.admin')

@section('title', 'Tambah Client')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Client</h1>
            <p class="page-subtitle">
                Tambahkan data client baru ke sistem
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.clients.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>


    {{-- =====================================================
    | FORM CARD
    ===================================================== --}}
    <div class="card card-hover">

        <div class="card-header">
            <h3 class="card-title">Form Client</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('keuangan.clients.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- NAMA --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Client</label>
                        <input type="text"
                               name="nama"
                               class="form-control"
                               required>
                    </div>

                    {{-- TIPE --}}
                    <div class="col-md-6">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-select">
                            <option value="b2c">B2C</option>
                            <option value="b2b">B2B</option>
                        </select>
                    </div>

                    {{-- PIC --}}
                    <div class="col-md-6">
                        <label class="form-label">PIC</label>
                        <input type="text"
                               name="pic"
                               class="form-control"
                               placeholder="Opsional">
                    </div>

                    {{-- TELEPON --}}
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text"
                               name="telepon"
                               class="form-control">
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control">
                    </div>

                    {{-- NPWP --}}
                    <div class="col-md-6">
                        <label class="form-label">NPWP</label>
                        <input type="text"
                               name="npwp"
                               class="form-control">
                    </div>

                    {{-- ALAMAT --}}
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="card-footer text-end mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Client
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
