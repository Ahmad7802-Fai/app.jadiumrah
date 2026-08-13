@extends('layouts.admin')

@section('title','Detail Invoice')

@section('content')
<div class="page">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">Detail Invoice</h4>
                <p class="text-muted small mb-0">
                    #{{ $invoice->nomor_invoice }}
                </p>
            </div>

            <div class="page-action">
                <a href="{{ route('keuangan.invoice-jamaah.index') }}"
                   class="btn btn-light btn-sm">
                    ← Kembali
                </a>

                {{-- <a href="{{ route('keuangan.invoice-jamaah.print-premium',$invoice->id) }}"
                   target="_blank"
                   class="btn btn-dark btn-sm">
                    <i class="fas fa-print me-2"></i> PDF
                </a> --}}

                <a href="{{ route('keuangan.payments.create',['invoice_id'=>$invoice->id]) }}"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-wallet me-2"></i> Pembayaran
                </a>
            </div>
        </div>
    </div>

    @include('components.alert')

    {{-- =====================================================
    SUMMARY CARD
    ====================================================== --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-4">

                <div class="col-lg-4">
                    <div class="card-stat">
                        <div class="stat-label">Jamaah</div>
                        <div class="stat-value">{{ $jamaah->nama_lengkap }}</div>
                        <small class="text-muted">{{ $jamaah->no_id }}</small>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="card-stat">
                        <div class="stat-label">Total</div>
                        <div class="stat-value text-danger">
                            Rp {{ number_format($invoice->total_tagihan) }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="card-stat">
                        <div class="stat-label">Terbayar</div>
                        <div class="stat-value text-success">
                            Rp {{ number_format($total_terbayar) }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="card-stat">
                        <div class="stat-label">Sisa</div>
                        <div class="stat-value">
                            Rp {{ number_format($sisa_tagihan) }}
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 d-flex align-items-center">
                    @if($invoice->status === 'lunas')
                        <span class="badge badge-soft-success">LUNAS</span>
                    @elseif($invoice->status === 'cicilan')
                        <span class="badge badge-soft-warning">CICILAN</span>
                    @else
                        <span class="badge badge-soft-danger">BELUM LUNAS</span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- =====================================================
    TABS CARD
    ====================================================== --}}
    <div class="card">
        <div class="card-header">
            <div class="tabs tabs-compact w-100">

                <div class="tabs-nav">
                    <div class="tab-item active" data-tab="detail">
                        <i class="fas fa-info-circle tab-icon"></i> Detail
                    </div>

                    <div class="tab-item" data-tab="history">
                        <i class="fas fa-money-bill tab-icon"></i>
                        History
                        <span class="badge badge-soft-secondary tab-badge">
                            {{ $history->count() }}
                        </span>
                    </div>

                    <div class="tab-item" data-tab="logs">
                        <i class="fas fa-clock tab-icon"></i> Logs
                    </div>
                </div>

            </div>
        </div>

        <div class="card-body">

            {{-- ================= DETAIL TAB ================= --}}
            <div class="tab-content active" id="tab-detail">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-row">
                            <div class="row-item">
                                <span class="label">Paket</span>
                                <span class="value">
                                    {{ $jamaah->paketMaster->nama_paket ?? '-' }}
                                </span>
                            </div>
                            <div class="row-item">
                                <span class="label">Kamar</span>
                                <span class="value">{{ $jamaah->tipe_kamar ?? '-' }}</span>
                            </div>
                            <div class="row-item">
                                <span class="label">Tanggal Daftar</span>
                                <span class="value">
                                    {{ $jamaah->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= HISTORY TAB ================= --}}
            <div class="tab-content" id="tab-history">
                <div class="table-responsive">
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($history as $h)
                            <tr>
                                <td data-label="Tanggal">
                                    {{ $h->tanggal_bayar->format('d M Y H:i') }}
                                </td>
                                <td data-label="Metode">
                                    <span class="badge badge-soft-success">
                                        {{ strtoupper($h->metode) }}
                                    </span>
                                </td>
                                <td data-label="Jumlah">
                                    Rp {{ number_format($h->jumlah) }}
                                </td>
                                <td data-label="Keterangan">
                                    {{ $h->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="table-empty">
                                    Belum ada pembayaran
                                </td>
                            </tr>
                        @endforelse
                        </tbody>

                        @if($history->count())
                        <tfoot>
                            <tr>
                                <th colspan="2" class="table-right">Total Terbayar</th>
                                <th colspan="2">
                                    Rp {{ number_format($total_terbayar) }}
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- ================= LOGS TAB ================= --}}
            <div class="tab-content" id="tab-logs">
            @forelse($logs as $log)
                <div class="pb-3 mb-3 border-bottom">

                    {{-- ACTION --}}
                    <div class="fw-semibold">
                        {{ strtoupper($log->action) }}
                    </div>

                    {{-- TIME --}}
                    <div class="small text-muted">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </div>

                    @php
                        $meta = is_array($log->meta)
                            ? $log->meta
                            : json_decode($log->meta, true);
                    @endphp

                    {{-- ACTOR --}}
                    <div class="small mt-1">
                        <strong>Oleh:</strong>
                        {{ $log->context }}
                        @if(isset($meta['actor']))
                            ({{ $meta['actor'] }})
                        @endif
                    </div>

                    {{-- DETAIL --}}
                    @if(is_array($meta))
                        <div class="small text-muted mt-2">
                            @if(isset($meta['amount']))
                                <div>
                                    <strong>Amount:</strong>
                                    Rp {{ number_format($meta['amount']) }}
                                </div>
                            @endif

                            @if(isset($meta['method']))
                                <div>
                                    <strong>Method:</strong>
                                    {{ strtoupper($meta['method']) }}
                                </div>
                            @endif

                            @if(isset($meta['invoice_id']))
                                <div>
                                    <strong>Invoice ID:</strong>
                                    {{ $meta['invoice_id'] }}
                                </div>
                            @endif

                            @if(isset($meta['reason']))
                                <div class="text-danger">
                                    <strong>Reason:</strong>
                                    {{ $meta['reason'] }}
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            @empty
                <p class="text-muted">Tidak ada aktivitas.</p>
            @endforelse
            </div>


        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'))
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'))

        tab.classList.add('active')
        document.getElementById('tab-' + tab.dataset.tab)
            ?.classList.add('active')
    })
})
</script>
@endpush
