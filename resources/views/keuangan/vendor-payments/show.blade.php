@extends('layouts.admin')

@section('title','Detail Pembayaran Vendor')

@section('content')
<div class="page-container container-narrow">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Pembayaran Vendor</h1>
            <p class="text-muted text-sm">
                Informasi lengkap pembayaran vendor layanan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.vendor-payments.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>


    {{-- =====================================================
    DETAIL CARD
    ====================================================== --}}
    <div class="card">
        <div class="card-body">

            <div class="row g-3">

                {{-- VENDOR --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Vendor</div>
                    <div class="fw-semibold">
                        {{ $payment->vendor_name }}
                    </div>
                </div>

                {{-- ITEM --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Item Layanan</div>
                    <div>
                        {{ $payment->layananItem->nama_item ?? '-' }}
                    </div>
                </div>

                {{-- INVOICE --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Invoice Vendor</div>
                    <div>
                        {{ $payment->invoice_number ?? '-' }}
                    </div>
                </div>

                {{-- JUMLAH --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Jumlah</div>
                    <div class="fw-bold text-success">
                        Rp {{ number_format($payment->amount,0,',','.') }}
                    </div>
                </div>

                {{-- TANGGAL --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Tanggal Pembayaran</div>
                    <div>
                        {{ date('d M Y', strtotime($payment->payment_date)) }}
                    </div>
                </div>

                {{-- STATUS --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Status</div>
                    <div>
                        @if($payment->status === 'approved')
                            <span class="badge bg-success">APPROVED</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge bg-warning text-dark">PENDING</span>
                        @else
                            <span class="badge bg-danger">REJECTED</span>
                        @endif
                    </div>
                </div>

                {{-- METODE --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Metode Pembayaran</div>
                    <div>
                        {{ $payment->payment_method ?? '-' }}
                    </div>
                </div>

                {{-- BANK --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Bank</div>
                    <div>
                        {{ $payment->bank ?? '-' }}
                    </div>
                </div>

                {{-- REFERENSI --}}
                <div class="col-md-4">
                    <div class="text-muted text-xs">Nomor Referensi</div>
                    <div>
                        {{ $payment->reference_no ?? '-' }}
                    </div>
                </div>

                {{-- CATATAN --}}
                <div class="col-12">
                    <div class="text-muted text-xs">Catatan</div>
                    <div>
                        {{ $payment->notes ?: '-' }}
                    </div>
                </div>

                {{-- BUKTI --}}
                <div class="col-12">
                    <div class="text-muted text-xs mb-1">Bukti Pembayaran</div>

                    @if($payment->proof_file)
                        <a href="{{ asset('storage/'.$payment->proof_file) }}"
                           target="_blank"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-file-image me-1"></i>
                            Lihat Bukti
                        </a>
                    @else
                        <span class="text-muted text-sm">
                            Tidak ada bukti pembayaran
                        </span>
                    @endif
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('keuangan.vendor-payments.edit', $payment->id) }}"
                   class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>

                <form action="{{ route('keuangan.vendor-payments.destroy', $payment->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus pembayaran ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
