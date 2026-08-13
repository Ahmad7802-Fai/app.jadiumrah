@extends('layouts.admin')

@section('title','Detail Pembayaran')

@section('content')
<div class="container-fluid">

    {{-- ======================================================
    HEADER
    ======================================================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('keuangan.payments.index') }}"
               class="btn btn-light"
               style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">Detail Pembayaran</h4>
        </div>

        <a href="{{ route('keuangan.payments.index') }}"
           class="btn btn-light">
            ← Kembali
        </a>
    </div>

    @include('components.alert')

    @php
        $invoice = $payment->invoice;
    @endphp

    {{-- ======================================================
    INVOICE + DETAIL (TOP)
    ======================================================= --}}
    <div class="row g-4 mb-4">

        {{-- LEFT — INVOICE SUMMARY --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ringkasan Invoice</h6>

                    @if($invoice)
                        <div class="mb-2">
                            <small class="text-muted">Nomor Invoice</small><br>
                            <strong>{{ $invoice->nomor_invoice }}</strong>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Total Tagihan</small><br>
                            <strong>Rp {{ number_format($invoice->total_tagihan) }}</strong>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Total Terbayar</small><br>
                            <strong class="text-success">
                                Rp {{ number_format($invoice->total_terbayar) }}
                            </strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Sisa Tagihan</small><br>
                            <strong class="text-danger">
                                Rp {{ number_format($invoice->sisa_tagihan) }}
                            </strong>
                        </div>

                        @if($invoice->sisa_tagihan > 0)
                            <a href="{{ route('keuangan.payments.create',['invoice_id'=>$invoice->id]) }}"
                               class="btn btn-primary w-100 text-white rounded-pill"
                               style="">
                                + Tambah Cicilan
                            </a>
                        @endif
                    @else
                        <p class="text-muted fst-italic mb-0">
                            Invoice belum terbentuk (menunggu validasi).
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT — PAYMENT DETAIL --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Detail Pembayaran</h6>

                        <div class="d-flex gap-2">
                            @if($payment->status === 'valid')
                                <a href="{{ route('keuangan.payments.kwitansi.premium',$payment->id) }}"
                                   class="btn btn-outline-dark rounded-pill btn-sm px-3">
                                    <i class="fas fa-file-pdf"></i> Kwitansi
                                </a>
                            @endif

                            @if($payment->status === 'pending')
                                <a href="{{ route('keuangan.payments.edit',$payment->id) }}"
                                   class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        @if($payment->status === 'pending')
                            <span class="badge badge-soft-warning">PENDING</span>
                        @elseif($payment->status === 'valid')
                            <span class="badge badge-soft-success">VALID</span>
                        @else
                            <span class="badge badge-soft-danger">DITOLAK</span>
                        @endif
                    </div>

                    {{-- GRID --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Jamaah</small><br>
                            <strong>{{ $payment->jamaah->nama_lengkap }}</strong>
                            <div class="small text-muted">{{ $payment->jamaah->no_id }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Jumlah</small><br>
                            <strong>Rp {{ number_format($payment->jumlah) }}</strong>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Metode</small><br>
                            <span class="badge bg-dark px-3">
                                {{ strtoupper($payment->metode) }}
                            </span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Tanggal</small><br>
                            <strong>{{ date('d M Y', strtotime($payment->tanggal_bayar)) }}</strong>
                        </div>
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-3">
                        <small class="text-muted">Keterangan</small>
                        <div class="border bg-light rounded-3 p-2">
                            {{ $payment->keterangan ?: '-' }}
                        </div>
                    </div>

                    {{-- BUKTI --}}
                    @if($payment->bukti_transfer)
                        <a href="{{ asset('storage/'.$payment->bukti_transfer) }}"
                           target="_blank"
                           class="btn btn-outline-dark rounded-pill btn-sm px-3">
                            <i class="fas fa-file"></i> Lihat Bukti
                        </a>
                    @endif

                    {{-- APPROVE --}}
                    @if($payment->status === 'pending')
                        <button class="btn btn-success w-100 rounded-pill py-2 fw-bold mt-4"
                                data-bs-toggle="modal"
                                data-bs-target="#approveModal">
                            <i class="fas fa-check me-2"></i> Validasi Pembayaran
                        </button>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================
    TABS
    ======================================================= --}}
    <div class="tabs tabs-compact">

        <div class="tabs-nav tabs-sticky">
            <div class="tab-item active" data-tab="detail">
                <i class="fas fa-info-circle tab-icon"></i> Detail
            </div>

            <div class="tab-item" data-tab="history">
                <i class="fas fa-receipt tab-icon"></i> History
                <span class="badge bg-success tab-badge">{{ $history->count() }}</span>
            </div>

            <div class="tab-item" data-tab="logs">
                <i class="fas fa-clock-rotate-left tab-icon"></i> Logs
            </div>
        </div>

        {{-- DETAIL --}}
        <div class="tab-content active" id="tab-detail">
            <div class="text-muted small">
                Informasi detail pembayaran dan invoice.
            </div>
        </div>

        {{-- HISTORY --}}
        <div class="tab-content" id="tab-history">
            @php $totalCicilan = 0; @endphp

            <div class="card shadow-sm border-0 rounded-4">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th class="text-end">Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($history as $i => $h)
                            @php $totalCicilan += $h->jumlah; @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ date('d M Y', strtotime($h->tanggal_bayar)) }}</td>
                                <td><span class="badge bg-success">{{ strtoupper($h->metode) }}</span></td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($h->jumlah) }}
                                </td>
                                <td class="text-muted">{{ $h->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada cicilan valid
                                </td>
                            </tr>
                        @endforelse
                        </tbody>

                        @if($history->count())
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Cicilan</th>
                                <th class="text-end text-success fw-bold">
                                    Rp {{ number_format($totalCicilan) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- LOGS --}}
        <div class="tab-content" id="tab-logs">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    @foreach($logs as $log)
                        <div class="pb-3 mb-3 border-bottom">
                            <strong>{{ ucfirst($log->action) }}</strong>
                            <div class="small text-muted">
                                {{ date('d M Y H:i', strtotime($log->created_at)) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ======================================================
APPROVE MODAL
====================================================== --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('keuangan.payments.approve',$payment->id) }}"
              method="POST"
              class="modal-content rounded-4">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Validasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin memvalidasi pembayaran ini?
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success rounded-pill px-4">
                    Ya, Validasi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'))
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'))

            tab.classList.add('active')
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active')
        })
    })
})
</script>
@endpush
