@extends('layouts.admin')

@section('title', 'Tagihan Invoice Jamaah')

@section('content')
<div class="page-container">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tagihan Invoice Jamaah</h1>
            <p class="text-muted text-sm">
                Pantau tagihan, cicilan, dan status invoice jamaah
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.payments.index') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-cash-register me-1"></i>
                Pembayaran
            </a>
        </div>
    </div>


    {{-- =====================================================
    FILTER
    ====================================================== --}}
    <form method="GET" class="card content-card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                <div class="col-md-5">
                    <label class="form-label text-sm">Cari Invoice</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Nama, No ID, Invoice">
                </div>

                <div class="col-md-4">
                    <label class="form-label text-sm">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" @selected(request('status')=='belum_lunas')>Belum Lunas</option>
                        <option value="cicilan"     @selected(request('status')=='cicilan')>Cicilan</option>
                        <option value="lunas"       @selected(request('status')=='lunas')>Lunas</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('keuangan.invoice-jamaah.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>


    {{-- =====================================================
    DESKTOP TABLE
    ====================================================== --}}
    <div class="card content-card p-0">
    <div class="table-responsive">

        <table class="table table-compact mb-0">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Jamaah</th>
                    <th>No Invoice</th>
                    <th>Total</th>
                    <th>Terbayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th class="col-actions table-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($invoices as $i => $inv)

                @php
                    $latestPayment = $inv->payments()
                        ->where('status','valid')
                        ->where(fn($q)=>$q->whereNull('is_deleted')->orWhere('is_deleted',0))
                        ->latest('tanggal_bayar')
                        ->first();
                @endphp

                <tr>
                    <td data-label="#">
                        {{ $invoices->firstItem() + $i }}
                    </td>

                    <td data-label="Jamaah">
                        <strong>{{ optional($inv->jamaah)->nama_lengkap ?? '-' }}</strong><br>
                        <span class="text-muted text-sm">
                            {{ optional($inv->jamaah)->no_id ?? '-' }}
                        </span>
                    </td>

                    <td data-label="No Invoice">
                        {{ $inv->nomor_invoice }}
                    </td>

                    <td data-label="Total">
                        Rp {{ number_format($inv->total_tagihan) }}
                    </td>

                    <td data-label="Terbayar">
                        Rp {{ number_format($inv->total_terbayar) }}
                    </td>

                    <td data-label="Sisa">
                        <strong>Rp {{ number_format($inv->sisa_tagihan) }}</strong>
                    </td>

                    <td data-label="Status">
                        <span class="table-status">
                            @if($inv->status === 'lunas')
                                <span class="badge bg-success">LUNAS</span>
                            @elseif($inv->status === 'cicilan')
                                <span class="badge bg-warning text-dark">CICILAN</span>
                            @else
                                <span class="badge bg-danger">BELUM</span>
                            @endif
                        </span>
                    </td>

                    <td class="col-actions table-right" data-label="Aksi">
                        <div class="table-actions">

                            <a href="{{ route('keuangan.invoice-jamaah.show',$inv->id) }}"
                               class="btn btn-outline-secondary btn-xs"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($latestPayment)
                            <a href="{{ route('keuangan.payments.kwitansi.premium',$latestPayment->id) }}"
                               target="_blank"
                               class="btn btn-outline-danger btn-xs"
                               title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @endif

                            <a href="{{ route('keuangan.payments.create',['invoice_id'=>$inv->id]) }}"
                               class="btn btn-outline-primary btn-xs"
                               title="Cicilan">
                                <i class="fas fa-money-bill-wave"></i>
                            </a>

                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="8" class="table-empty">
                        Belum ada invoice jamaah
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    <div class="p-3">
        {{ $invoices->links() }}
    </div>

    </div>
        {{-- =====================================================
        FAB — MOBILE
        ====================================================== --}}
            <div class="fab d-md-none">
                <a href="{{ route('keuangan.payments.create') }}"
                class="fab-btn"
                aria-label="Tambah Pembayaran">
                    <i class="fas fa-plus"></i>
                </a>
            </div>


    </div>
@endsection
