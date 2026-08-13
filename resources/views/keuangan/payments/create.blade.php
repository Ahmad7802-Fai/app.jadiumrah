@extends('layouts.admin')

@section('title','Tambah Pembayaran')

@section('content')
<div class="container-fluid">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header mb-4">

        <div class="d-flex align-items-center gap-3">

            {{-- BACK MOBILE --}}
            <a href="{{ route('keuangan.payments.index') }}"
               class="btn btn-light rounded-circle d-md-none"
               style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h4 class="fw-bold mb-1">
                    {{ $mode === 'cicilan' ? 'Tambah Cicilan' : 'Tambah Pembayaran' }}
                </h4>

                <small class="text-muted">
                    @if($mode === 'cicilan')
                        Tambah cicilan untuk invoice
                        <strong>{{ $invoice->nomor_invoice }}</strong>
                    @else
                        Pembayaran baru akan berstatus
                        <strong class="text-warning">PENDING</strong>
                        dan wajib divalidasi keuangan
                    @endif
                </small>
            </div>

        </div>

        {{-- ACTION DESKTOP --}}
        <div class="page-actions">
            <a href="{{ route('keuangan.payments.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

    </div>

    @include('components.alert')

    {{-- ===============================
       FORM CARD
    ================================ --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('keuangan.payments.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="form-payment">
                @csrf

                {{-- ===============================
                   MODE CICILAN
                ================================ --}}
                @if($mode === 'cicilan')

                    <input type="hidden" name="jamaah_id" value="{{ $jamaah->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jamaah</label>
                        <input class="form-control bg-light"
                               value="{{ $jamaah->nama_lengkap }} ({{ $jamaah->no_id }})"
                               disabled>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Tagihan</label>
                            <input class="form-control bg-light"
                                   value="Rp {{ number_format($total_tagihan) }}"
                                   disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sisa Tagihan</label>
                            <input class="form-control bg-light"
                                   value="Rp {{ number_format($sisa_tagihan) }}"
                                   disabled>
                        </div>
                    </div>

                @else

                {{-- ===============================
                   MODE PEMBAYARAN BARU
                ================================ --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Jamaah *</label>
                    <select id="jamaahSelect"
                            name="jamaah_id"
                            class="form-select"
                            required></select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total Tagihan</label>
                        <input id="total_tagihan"
                               class="form-control bg-light"
                               value="Rp 0"
                               disabled>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Sisa Tagihan</label>
                        <input id="sisa_tagihan"
                               class="form-control bg-light"
                               value="Rp 0"
                               disabled>
                    </div>
                </div>

                @endif

                {{-- ===============================
                   FORM PEMBAYARAN
                ================================ --}}
                <div class="mt-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran *</label>
                        <select name="metode" class="form-select" required>
                            <option value="transfer">Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="kantor">Kantor</option>
                            <option value="gateway">Gateway</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Pembayaran *</label>
                        <input type="number"
                               name="jumlah"
                               class="form-control"
                               min="1000"
                               @if($mode === 'cicilan') max="{{ $sisa_tagihan }}" @endif
                               required>

                        @if($mode === 'cicilan')
                            <small class="text-muted">
                                Maksimal Rp {{ number_format($sisa_tagihan) }}
                            </small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Bayar *</label>
                        <input type="date" name="tanggal_bayar"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bukti Transfer</label>
                        <input type="file"
                               name="bukti_transfer"
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">JPG / PNG / PDF max 4MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan"
                                  rows="3"
                                  class="form-control"></textarea>
                    </div>

                </div>

                {{-- ===============================
                   ACTION
                ================================ --}}
                <div class="d-flex justify-content-center mt-4">
                    <button class="btn btn-primary px-5" id="btnSubmit">
                        <i class="fas fa-save me-1"></i>
                        Simpan Pembayaran
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

{{-- ====================================================
SCRIPTS
==================================================== --}}
@push('scripts')
<script>
$(document).ready(function () {

@if(isset($mode) && $mode === 'baru')

const $jamaah = $('#jamaahSelect');
const $total  = $('#total_tagihan');
const $sisa   = $('#sisa_tagihan');

$jamaah.select2({
    placeholder: "Cari Jamaah...",
    minimumInputLength: 2,
    width: '100%',
    ajax: {
        url: "{{ route('keuangan.payments.search-jamaah') }}",
        delay: 300,
        dataType: 'json',
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true
    }
});

$jamaah.on('select2:select', function (e) {
    const jamaahId = e.params.data.id;

    $total.val('Rp 0');
    $sisa.val('Rp 0');

    fetch(`{{ url('keuangan/pembayaran/ajax-invoice') }}/${jamaahId}`)
        .then(res => res.json())
        .then(res => {
            let total = 0;
            let sisa  = 0;

            if (res.invoice) {
                total = res.invoice.total_tagihan;
                sisa  = res.invoice.sisa_tagihan;
            } else {
                total = res.rekomendasi_total_tagihan;
                sisa  = res.rekomendasi_total_tagihan;
            }

            $total.val('Rp ' + Number(total).toLocaleString('id-ID'));
            $sisa.val('Rp ' + Number(sisa).toLocaleString('id-ID'));
        });
});

@endif

$('#form-payment').on('submit', function () {
    $(this).find('button[type=submit]')
        .prop('disabled', true)
        .text('Menyimpan...');
});

});
</script>
@endpush
