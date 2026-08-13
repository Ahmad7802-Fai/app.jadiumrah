{{-- resources/views/keuangan/payments/create-cicilan.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Cicilan')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('keuangan.payments.index') }}"
           class="btn btn-light shadow-sm rounded-circle d-md-none me-3"
           aria-label="Kembali mobile"
           style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div>
            <h4 class="fw-bold mb-1">Tambah Cicilan</h4>
            <p class="text-muted small mb-0">Menambah cicilan untuk invoice <strong class="text-success">{{ $invoice->nomor_invoice }}</strong>.</p>
        </div>

        <a href="{{ route('keuangan.payments.index') }}"
           class="btn-ju-outline ms-auto d-none d-md-inline-flex align-items-center">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success rounded-pill">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-pill">{{ session('error') }}</div>
    @endif

    {{-- Card --}}
    <div class="card-premium">
        <div class="card-body">
            <form action="{{ route('keuangan.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Hidden invoice & jamaah --}}
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                <input type="hidden" name="jamaah_id" value="{{ $jamaah->id }}">

                {{-- Jamaah & Invoice Summary --}}
                <div class="mb-3">
                    <label class="fw-semibold">Jamaah</label>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <div class="fw-bold">{{ $jamaah->nama_lengkap }}</div>
                            <small class="text-muted">{{ $jamaah->no_id }}</small>
                        </div>
                        <div class="ms-auto text-end">
                            <small class="text-muted">Invoice</small>
                            <div class="fw-semibold">{{ $invoice->nomor_invoice }}</div>
                        </div>
                    </div>
                </div>

                {{-- Tagihan --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="fw-semibold">Total Tagihan</label>
                        <input type="text" class="form-control bg-light rounded-pill" value="Rp {{ number_format($invoice->total_tagihan) }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Total Terbayar</label>
                        <input type="text" class="form-control bg-light rounded-pill" value="Rp {{ number_format($invoice->total_terbayar ?? 0) }}" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-semibold">Sisa Tagihan</label>
                        <input type="text" class="form-control bg-light rounded-pill" value="Rp {{ number_format(($invoice->total_tagihan - ($invoice->total_terbayar ?? 0))) }}" disabled>
                    </div>
                </div>

                {{-- Cicilan Form --}}
                <div class="mb-3">
                    <label class="fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                    <select name="metode" class="form-control rounded-pill" required>
                        <option value="transfer">Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="kantor">Kantor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Jumlah Cicilan <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control rounded-pill" placeholder="0" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_bayar" class="form-control rounded-pill" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Bukti (opsional)</label>
                    <input type="file" name="bukti_transfer" class="form-control rounded-pill" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn-ju px-5 py-2">
                        <i class="fas fa-save me-2"></i> Simpan Cicilan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
