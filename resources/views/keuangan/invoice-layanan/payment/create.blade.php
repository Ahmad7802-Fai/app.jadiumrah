@extends('layouts.admin')

@section('title','Tambah Pembayaran Invoice')

@section('content')
<div class="page-container">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">
                Tambah Pembayaran
            </h1>
            <p class="text-muted text-sm">
                Invoice <strong>#{{ $invoice->no_invoice }}</strong>
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.invoice-layanan.show',$invoice->id) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- =====================================================
    INVOICE SUMMARY
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <div class="card-title">Ringkasan Invoice</div>
                <div class="card-subtitle">
                    {{ $invoice->transaksi->client->nama }}
                </div>
            </div>
        </div>

        <div class="card-body">
            @php $sisa = $invoice->amount - $invoice->paid_amount; @endphp

            <div class="row g-3 text-sm">
                <div class="col-md-4">
                    <div class="text-muted">Total Tagihan</div>
                    <div class="fw-semibold text-danger">
                        Rp {{ number_format($invoice->amount,0,',','.') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted">Sudah Dibayar</div>
                    <div class="fw-semibold text-success">
                        Rp {{ number_format($invoice->paid_amount,0,',','.') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted">Sisa Tagihan</div>
                    <div class="fw-semibold text-primary">
                        Rp {{ number_format($sisa,0,',','.') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted">Status</div>
                    @if($sisa <= 0)
                        <span class="badge bg-success">Lunas</span>
                    @elseif($invoice->paid_amount > 0)
                        <span class="badge bg-warning text-dark">Parsial</span>
                    @else
                        <span class="badge bg-danger">Belum Dibayar</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
    FORM PEMBAYARAN
    ====================================================== --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Form Pembayaran</div>
        </div>

        <form action="{{ route('keuangan.payment-layanan.store',$invoice->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Jumlah Pembayaran <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="amount"
                           class="form-control @error('amount') is-invalid @enderror"
                           min="1"
                           required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Metode Pembayaran
                    </label>
                    <select name="payment_method" class="form-control">
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Bank (Opsional)
                    </label>
                    <input type="text"
                           name="bank"
                           class="form-control"
                           placeholder="BCA, Mandiri, BSI">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Nomor Referensi
                    </label>
                    <input type="text"
                           name="reference_no"
                           class="form-control"
                           placeholder="Nomor referensi transfer">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Bukti Pembayaran
                    </label>
                    <input type="file"
                           name="proof"
                           class="form-control">
                    <small class="text-muted">
                        JPG, PNG, atau PDF
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Catatan
                    </label>
                    <textarea name="catatan"
                              rows="3"
                              class="form-control"></textarea>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-primary">
                    <i class="fas fa-credit-card me-1"></i>
                    Simpan Pembayaran
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
