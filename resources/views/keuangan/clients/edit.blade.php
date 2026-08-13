@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Client</h1>
            <p class="page-subtitle">
                Perbarui data client yang terdaftar
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
            <h3 class="card-title">Form Edit Client</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('keuangan.clients.update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- NAMA --}}
                    <div class="col-md-6">
                        <label class="form-label">Nama Client</label>
                        <input type="text"
                               name="nama"
                               class="form-control"
                               value="{{ $client->nama }}"
                               required>
                    </div>

                    {{-- TIPE --}}
                    <div class="col-md-6">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-select">
                            <option value="b2c" @selected($client->tipe === 'b2c')>B2C</option>
                            <option value="b2b" @selected($client->tipe === 'b2b')>B2B</option>
                        </select>
                    </div>

                    {{-- PIC --}}
                    <div class="col-md-6">
                        <label class="form-label">PIC</label>
                        <input type="text"
                               name="pic"
                               class="form-control"
                               value="{{ $client->pic }}"
                               placeholder="Opsional">
                    </div>

                    {{-- TELEPON --}}
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text"
                               name="telepon"
                               class="form-control"
                               value="{{ $client->telepon }}">
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ $client->email }}">
                    </div>

                    {{-- NPWP --}}
                    <div class="col-md-6">
                        <label class="form-label">NPWP</label>
                        <input type="text"
                               name="npwp"
                               class="form-control"
                               value="{{ $client->npwp }}">
                    </div>

                    {{-- ALAMAT --}}
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat"
                                  class="form-control"
                                  rows="3">{{ $client->alamat }}</textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="card-footer text-end mt-3">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Update Client
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
