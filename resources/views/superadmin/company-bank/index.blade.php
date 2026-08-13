@extends('layouts.admin')

@section('title', 'Company Bank Account')

@section('content')
@php
    $tab = 'bank';
@endphp

<div class="page-container">

    {{-- =========================
     | PAGE HEADER
     ========================= --}}
    <div class="mb-3">
        <h1 class="h3 fw-bold">Company Settings</h1>
        <p class="text-muted">
            Kelola rekening bank perusahaan untuk invoice, tabungan, refund, dan operasional
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

            <a href="{{ route('superadmin.company-profile.index') }}"
               class="tab-item">
                <i class="fa fa-building tab-icon"></i>
                Company Profile
            </a>

            <a href="{{ route('superadmin.company-profile.index', ['tab' => 'document']) }}"
               class="tab-item">
                <i class="fa fa-file-signature tab-icon"></i>
                Dokumen & TTD
            </a>

            <a href="{{ route('superadmin.company-profile.index', ['tab' => 'logo']) }}"
               class="tab-item">
                <i class="fa fa-image tab-icon"></i>
                Logo
            </a>

            <a href="{{ route('superadmin.company-bank.index') }}"
               class="tab-item active">
                <i class="fa fa-university tab-icon"></i>
                Rekening Bank
            </a>

        </div>

        {{-- =========================
         | TAB CONTENT — BANK
         ========================= --}}
        <div class="tab-content active">

            {{-- =========================
             | ADD BANK FORM
             ========================= --}}
            <div class="card mb-4">
                <div class="card-header fw-bold">
                    Tambah Rekening Bank
                </div>

                <form method="POST"
                      action="{{ route('superadmin.company-bank.store') }}">
                    @csrf

                    <div class="card-body row g-3">

                        <div class="col-md-3">
                            <label class="form-label">Nama Bank</label>
                            <input type="text"
                                   name="bank_name"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">No. Rekening</label>
                            <input type="text"
                                   name="account_number"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Atas Nama</label>
                            <input type="text"
                                   name="account_name"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Purpose</label>
                            <select name="purpose" class="form-select">
                                <option value="invoice">Invoice</option>
                                <option value="tabungan">Tabungan</option>
                                <option value="refund">Refund</option>
                                <option value="operational">Operasional</option>
                            </select>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_default"
                                       value="1">
                                <label class="form-check-label">
                                    Default
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary btn-sm">
                            Simpan Rekening
                        </button>
                    </div>
                </form>
            </div>

            {{-- =========================
             | BANK LIST
             ========================= --}}
            <div class="card">
                <div class="card-header fw-bold">
                    Daftar Rekening Bank ({{ ucfirst($purpose) }})
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th>No. Rekening</th>
                                <th>Atas Nama</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($banks as $bank)
                            <tr>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->account_number }}</td>
                                <td>{{ $bank->account_name }}</td>
                                <td>{{ ucfirst($bank->purpose) }}</td>
                                <td>
                                    @if($bank->is_default)
                                        <span class="badge bg-success">Default</span>
                                    @elseif(!$bank->is_active)
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @else
                                        <span class="badge bg-light text-dark">Aktif</span>
                                    @endif
                                </td>
                                <td class="text-end">

                                    {{-- SET DEFAULT --}}
                                    @if($bank->is_active && !$bank->is_default)
                                        <form method="POST"
                                              action="{{ route('superadmin.company-bank.default', $bank) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success">
                                                Jadikan Default
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DEACTIVATE --}}
                                    @if($bank->is_active && !$bank->is_default)
                                        <form method="POST"
                                              action="{{ route('superadmin.company-bank.deactivate', $bank) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-danger">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ACTIVATE --}}
                                    @if(!$bank->is_active)
                                        <form method="POST"
                                              action="{{ route('superadmin.company-bank.activate', $bank) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-primary">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada rekening bank
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
