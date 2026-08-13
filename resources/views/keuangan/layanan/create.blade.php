@extends('layouts.admin')

@section('title', 'Tambah Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Layanan</h1>
            <p class="page-subtitle">
                Tambahkan layanan baru ke dalam sistem
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.layanan.index') }}"
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

        <div class="card-body card-body-lg">

            <form action="{{ route('keuangan.layanan.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- KODE --}}
                    <div class="col-md-4">
                        <label class="form-label">Kode Layanan</label>
                        <input type="text"
                               name="kode_layanan"
                               class="form-control"
                               placeholder="LYN-001"
                               required>
                    </div>

                    {{-- NAMA --}}
                    <div class="col-md-8">
                        <label class="form-label">Nama Layanan</label>
                        <input type="text"
                               name="nama_layanan"
                               class="form-control"
                               placeholder="Hotel, Visa, Transport, dll"
                               required>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="ticket">Ticket</option>
                            <option value="visa">Visa</option>
                            <option value="land">Land Arrangement</option>
                            <option value="other">Lain-lain</option>
                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Detail layanan (opsional)"></textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-4 d-flex justify-content-end">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan Layanan
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
