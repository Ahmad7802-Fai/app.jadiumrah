@extends('layouts.admin')

@section('title','Tambah Pembayaran Vendor')

@section('content')
<div class="page-container">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Pembayaran Vendor</h1>
            <p class="text-muted text-sm">
                Catat pembayaran keluar kepada vendor layanan
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

            <form action="{{ route('keuangan.vendor-payments.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="row g-3">

                    {{-- ITEM LAYANAN --}}
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-semibold">
                            Item Layanan <span class="text-danger">*</span>
                        </label>
                        <select name="layanan_item_id"
                                class="form-control"
                                required>
                            <option value="">Pilih item layanan</option>
                            @foreach($layananItems as $item)
                                <option value="{{ $item->id }}">
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
                               placeholder="Nama vendor">
                    </div>

                    {{-- INVOICE VENDOR --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            No Invoice Vendor
                        </label>
                        <input type="text"
                               name="invoice_number"
                               class="form-control"
                               placeholder="Opsional">
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
                               placeholder="0">
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Tanggal Pembayaran <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="payment_date"
                               class="form-control"
                               required>
                    </div>

                    {{-- METODE --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Metode Pembayaran
                        </label>
                        <input type="text"
                               name="payment_method"
                               class="form-control"
                               placeholder="Transfer / Cash / QRIS">
                    </div>

                    {{-- BANK --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Bank
                        </label>
                        <input type="text"
                               name="bank"
                               class="form-control"
                               placeholder="Nama bank">
                    </div>

                    {{-- REFERENSI --}}
                    <div class="col-md-4">
                        <label class="form-label text-sm fw-semibold">
                            Nomor Referensi
                        </label>
                        <input type="text"
                               name="reference_no"
                               class="form-control"
                               placeholder="Opsional">
                    </div>

                    {{-- BUKTI --}}
                    <div class="col-12">
                        <label class="form-label text-sm fw-semibold">
                            Bukti Pembayaran
                        </label>
                        <input type="file"
                               name="proof_file"
                               class="form-control">
                        <small class="text-muted text-xs">
                            JPG / PNG / PDF (opsional)
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
                                  placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-4 text-end">
                    <button class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Pembayaran
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
