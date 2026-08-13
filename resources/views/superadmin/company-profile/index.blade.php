@extends('layouts.admin')

@section('title', 'Company Settings')

@section('content')
@php
    $tab = request('tab', 'profile');
@endphp

<div class="page-container">

    {{-- =========================
     | PAGE HEADER
     ========================= --}}
    <div class="mb-3">
        <h1 class="h3 fw-bold">Company Settings</h1>
        <p class="text-muted">
            Master data perusahaan, dokumen resmi, logo, dan rekening bank
        </p>
    </div>

    {{-- =========================
     | FLASH MESSAGE
     ========================= --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- =========================
     | TABS WRAPPER
     ========================= --}}
    <div class="tabs tabs-compact">

        {{-- =========================
         | TAB NAV
         ========================= --}}
        <div class="tabs-nav tabs-sticky">

            <a href="?tab=profile"
               class="tab-item {{ $tab === 'profile' ? 'active' : '' }}">
                <i class="fa fa-building tab-icon"></i>
                Company Profile
            </a>

            <a href="?tab=document"
               class="tab-item {{ $tab === 'document' ? 'active' : '' }}">
                <i class="fa fa-file-signature tab-icon"></i>
                Dokumen & TTD
            </a>

            <a href="?tab=logo"
               class="tab-item {{ $tab === 'logo' ? 'active' : '' }}">
                <i class="fa fa-image tab-icon"></i>
                Logo
            </a>

            <a href="{{ route('superadmin.company-bank.index') }}"
               class="tab-item">
                <i class="fa fa-university tab-icon"></i>
                Rekening Bank
            </a>

        </div>

        {{-- =========================
         | TAB CONTENT — PROFILE
         ========================= --}}
        <div class="tab-content {{ $tab === 'profile' ? 'active' : '' }}">

            <form method="POST" action="{{ route('superadmin.company-profile.store') }}">
                @csrf

                <div class="card mb-4">
                    <div class="card-header fw-bold">Informasi Perusahaan</div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama PT *</label>
                            <input type="text" name="name"
                                class="form-control"
                                required
                                value="{{ old('name', $company->name ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Brand</label>
                            <input type="text" name="brand_name"
                                class="form-control"
                                value="{{ old('brand_name', $company->brand_name ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                class="form-control"
                                value="{{ old('email', $company->email ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone"
                                class="form-control"
                                value="{{ old('phone', $company->phone ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="text" name="website"
                                class="form-control"
                                value="{{ old('website', $company->website ?? '') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="address" rows="3"
                                class="form-control">{{ old('address', $company->address ?? '') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city"
                                class="form-control"
                                value="{{ old('city', $company->city ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="province"
                                class="form-control"
                                value="{{ old('province', $company->province ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code"
                                class="form-control"
                                value="{{ old('postal_code', $company->postal_code ?? '') }}">
                        </div>

                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">
                        Simpan Company Profile
                    </button>
                </div>
            </form>

        </div>

        {{-- =========================
         | TAB CONTENT — DOCUMENT
         ========================= --}}
        <div class="tab-content {{ $tab === 'document' ? 'active' : '' }}">

            <form method="POST" action="{{ route('superadmin.company-profile.store') }}">
                @csrf

                <div class="card mb-4">
                    <div class="card-header fw-bold">Dokumen & Tanda Tangan</div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="npwp"
                                class="form-control"
                                value="{{ old('npwp', $company->npwp ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama NPWP</label>
                            <input type="text" name="npwp_name"
                                class="form-control"
                                value="{{ old('npwp_name', $company->npwp_name ?? '') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Alamat NPWP</label>
                            <textarea name="npwp_address" rows="2"
                                class="form-control">{{ old('npwp_address', $company->npwp_address ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Footer Invoice</label>
                            <textarea name="invoice_footer" rows="3"
                                class="form-control">{{ old('invoice_footer', $company->invoice_footer ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Footer Surat</label>
                            <textarea name="letter_footer" rows="3"
                                class="form-control">{{ old('letter_footer', $company->letter_footer ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Penandatangan</label>
                            <input type="text" name="signature_name"
                                class="form-control"
                                value="{{ old('signature_name', $company->signature_name ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="signature_position"
                                class="form-control"
                                value="{{ old('signature_position', $company->signature_position ?? '') }}">
                        </div>

                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary">
                        Simpan Dokumen
                    </button>
                </div>
            </form>

        </div>

        {{-- =========================
         | TAB CONTENT — LOGO
         ========================= --}}
        <div class="tab-content {{ $tab === 'logo' ? 'active' : '' }}">

            <div class="card">
                <div class="card-header fw-bold">Logo Perusahaan</div>
                <div class="card-body row g-4">

                    @foreach ([
                        'logo' => 'Logo Utama',
                        'invoice' => 'Logo Invoice',
                        'bw' => 'Logo Hitam Putih'
                    ] as $type => $label)

                        @php $field = $type === 'logo' ? 'logo' : 'logo_'.$type; @endphp

                        <div class="col-md-4">
                            <h6 class="mb-2">{{ $label }}</h6>

                            <div class="border rounded p-3 text-center mb-2"
                                 style="min-height:120px">
                                @if(!empty($company?->$field))
                                    <img src="{{ asset('storage/'.$company->$field) }}"
                                         class="img-fluid"
                                         style="max-height:90px">
                                @else
                                    <span class="text-muted">Belum ada logo</span>
                                @endif
                            </div>

                            <form method="POST"
                                  action="{{ route('superadmin.company-profile.logo', $type) }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="file"
                                       name="logo"
                                       class="form-control form-control-sm mb-2"
                                       required>
                                <button class="btn btn-sm btn-outline-primary w-100">
                                    Upload {{ $label }}
                                </button>
                            </form>
                        </div>

                    @endforeach

                </div>
            </div>

        </div>

    </div>
</div>
@endsection
