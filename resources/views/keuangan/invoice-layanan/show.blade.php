@extends('layouts.admin')

@section('title','Detail Invoice Layanan')

@section('content')
<div class="page-container container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Invoice</h1>
            <p class="text-muted text-sm">#{{ $invoice->no_invoice }}</p>
        </div>

        <div class="page-actions d-none d-md-flex">
            <a href="{{ route('keuangan.invoice-layanan.print',$invoice->id) }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-print me-1"></i> Print
            </a>

            <a href="{{ route('keuangan.payment-layanan.create',$invoice->id) }}"
               class="btn btn-primary">
                <i class="fas fa-wallet me-1"></i> Tambah Pembayaran
            </a>
        </div>
    </div>

    {{-- =====================================================
    CLIENT INFO
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <div class="card-title">Informasi Invoice</div>
                <div class="card-subtitle">
                    {{ $invoice->transaksi->client->nama ?? '-' }}
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3 text-sm">
                <div class="col-md-4">
                    <div class="text-muted">Tanggal Invoice</div>
                    <div class="fw-semibold">
                        {{ $invoice->created_at->format('d M Y') }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted">Status</div>
                    @if($invoice->status === 'paid')
                        <span class="badge bg-success">Lunas</span>
                    @elseif($invoice->status === 'partial')
                        <span class="badge bg-warning text-dark">Parsial</span>
                    @else
                        <span class="badge bg-danger">Belum Dibayar</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
    SUMMARY
    ====================================================== --}}
    @php
        $approvedPaid = $invoice->payments->where('status','approved')->sum('amount');
        $sisa = $invoice->amount - $approvedPaid;
    @endphp

    <div class="row g-3 mb-3">

        <div class="col-md-4">
            <div class="card card-stat">
                <div class="stat-label">Total Tagihan</div>
                <div class="stat-value text-danger">
                    Rp {{ number_format($invoice->amount,0,',','.') }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat">
                <div class="stat-label">Total Disetujui</div>
                <div class="stat-value text-success">
                    Rp {{ number_format($approvedPaid,0,',','.') }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat">
                <div class="stat-label">Sisa Tagihan</div>
                <div class="stat-value {{ $sisa <= 0 ? 'text-success' : 'text-primary' }}">
                    Rp {{ number_format($sisa,0,',','.') }}
                </div>
            </div>
        </div>

    </div>

    {{-- =====================================================
    DETAIL ITEM
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-header">
            <div class="card-title">Detail Item</div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th class="table-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->transaksi->items as $i => $it)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $it->item->nama_item ?? '-' }}</td>
                            <td>{{ $it->qty }}</td>
                            <td>Rp {{ number_format($it->harga,0,',','.') }}</td>
                            <td class="table-right fw-semibold">
                                Rp {{ number_format($it->subtotal,0,',','.') }}
                            </td>
                        </tr>
                        @endforeach

                        <tr class="bg-soft">
                            <td colspan="4" class="table-right fw-bold">TOTAL</td>
                            <td class="table-right fw-bold text-danger">
                                Rp {{ number_format($invoice->amount,0,',','.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =====================================================
    PAYMENT HISTORY
    ====================================================== --}}
    <div class="card mb-5">
        <div class="card-header">
            <div class="card-title">Riwayat Pembayaran</div>

            <a href="{{ route('keuangan.payment-layanan.create',$invoice->id) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-wallet me-1"></i> Tambah
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Bank</th>
                            <th>Ref</th>
                            <th class="table-right">Jumlah</th>
                            <th>Bukti</th>
                            <th class="table-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $i => $p)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>
                                @if($p->status=='approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($p->status=='rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>{{ $p->payment_method }}</td>
                            <td>{{ $p->bank ?? '-' }}</td>
                            <td>{{ $p->reference_no ?? '-' }}</td>
                            <td class="table-right fw-semibold">
                                Rp {{ number_format($p->amount,0,',','.') }}
                            </td>
                            <td>
                                @if($p->proof)
                                    <a href="{{ asset('storage/'.$p->proof) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-outline-secondary">
                                        <i class="fas fa-image"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="table-right">
                                @if($p->status=='pending')
                                    <form method="POST"
                                          action="{{ route('keuangan.invoice-layanan.payment.approve',[$invoice->id,$p->id]) }}"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-xs btn-success">
                                            Approve
                                        </button>
                                    </form>

                                    <button class="btn btn-xs btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#reject{{ $p->id }}">
                                        Reject
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="table-empty">
                                Belum ada pembayaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- =====================================================
FAB MOBILE
===================================================== --}}
<div class="fab d-md-none">
    <a href="{{ route('keuangan.payment-layanan.create',$invoice->id) }}"
       class="fab-btn">
        <i class="fas fa-wallet"></i>
    </a>
</div>

@endsection
