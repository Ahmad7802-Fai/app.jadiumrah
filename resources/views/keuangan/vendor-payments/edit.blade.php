@extends('layouts.admin')

@section('title','Edit Pembayaran Vendor')

@section('content')
<div class="page-container container-narrow">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Pembayaran Vendor</h1>
            <p class="text-muted text-sm">
                Perbarui data pembayaran vendor layanan
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
    FORM CARD
    ====================================================== --}}
    <div class="card">
        <div class="card-body">

            <form action="{{ route('keuangan.vendor-payments.update', $payment->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    {{-- ITEM LAYANAN --}}
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold">
                            Item Layanan <span class="text-danger">*</span>
                        </label>
                        <select name="layanan_item_id"
                                class="form-control"
                                required>
                            @foreach($layananItems as $item)
                                <option value="{{ $item->id }}"
                                    {{ $payment->layanan_item_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_item }} — {{ $item->vendor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- VENDOR --}}
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold">
                            Nama Vendor <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="vendor_name"
                               class="form-control"
                               required
                               value="{{ $payment->vendor_name }}">
                    </div>

                    {{-- INVOICE VENDOR --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            No Invoice Vendor
                        </label>
                        <input type="text"
                               name="invoice_number"
                               class="form-control"
                               value="{{ $payment->invoice_number }}">
                    </div>

                    {{-- JUMLAH --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Jumlah Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               name="amount"
                               class="form-control"
                               min="1"
                               required
                               value="{{ $payment->amount }}">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Tanggal Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="payment_date"
                               class="form-control"
                               required
                               value="{{ $payment->payment_date }}">
                    </div>

                    {{-- METODE --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Metode Pembayaran
                        </label>
                        <input type="text"
                               name="payment_method"
                               class="form-control"
                               value="{{ $payment->payment_method }}">
                    </div>

                    {{-- BANK --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Bank
                        </label>
                        <input type="text"
                               name="bank"
                               class="form-control"
                               value="{{ $payment->bank }}">
                    </div>

                    {{-- REFERENSI --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Nomor Referensi
                        </label>
                        <input type="text"
                               name="reference_no"
                               class="form-control"
                               value="{{ $payment->reference_no }}">
                    </div>

                    {{-- BUKTI --}}
                    <div class="col-12">
                        <label class="form-label text-sm fw-semibold">
                            Bukti Pembayaran
                        </label>

                        @if($payment->proof_file)
                            <div class="mb-2">
                                <a href="{{ asset('storage/'.$payment->proof_file) }}"
                                   target="_blank"
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-file-image me-1"></i>
                                    Lihat Bukti Lama
                                </a>
                            </div>
                        @endif

                        <input type="file"
                               name="proof_file"
                               class="form-control">

                        <small class="text-muted text-xs">
                            Upload baru jika ingin mengganti bukti
                        </small>
                    </div>

                    {{-- CATATAN --}}
                    <div class="col-12">
                        <label class="form-label text-sm fw-semibold">
                            Catatan
                        </label>
                        <textarea name="notes"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Catatan tambahan">{{ $payment->notes }}</textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-4 text-end">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Pembayaran
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
