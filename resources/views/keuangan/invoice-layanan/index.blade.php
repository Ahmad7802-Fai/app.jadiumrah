@extends('layouts.admin')

@section('title','Invoice Layanan')

@section('content')
<div class="page-container page-container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Invoice Layanan</h1>
            <p class="text-muted text-sm">
                Daftar tagihan dari transaksi layanan client
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.transaksi-layanan.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Buat Transaksi
            </a>
        </div>
    </div>

    {{-- =====================================================
    TABLE INVOICE
    ====================================================== --}}
    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>No Invoice</th>
                            <th>Client</th>
                            <th>Total</th>
                            <th>Dibayar</th>
                            <th>Sisa</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="col-actions"></th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($invoices as $i => $inv)
                        @php
                            $sisa = $inv->amount - $inv->paid_amount;
                        @endphp

                        <tr>
                            <td data-label="#">
                                {{ $invoices->firstItem() + $i }}
                            </td>

                            <td data-label="Invoice">
                                <strong>#{{ $inv->no_invoice }}</strong>
                            </td>

                            <td data-label="Client">
                                {{ $inv->transaksi->client->nama ?? '-' }}
                            </td>

                            <td data-label="Total" class="fw-semibold text-danger">
                                Rp {{ number_format($inv->amount,0,',','.') }}
                            </td>

                            <td data-label="Dibayar" class="fw-semibold text-success">
                                Rp {{ number_format($inv->paid_amount,0,',','.') }}
                            </td>

                            <td data-label="Sisa" class="fw-semibold text-primary">
                                Rp {{ number_format($sisa,0,',','.') }}
                            </td>

                            <td data-label="Status">
                                @if($sisa <= 0)
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($inv->paid_amount > 0)
                                    <span class="badge bg-warning text-dark">Parsial</span>
                                @else
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @endif
                            </td>

                            <td data-label="Tanggal">
                                {{ $inv->created_at->format('d M Y') }}
                            </td>

                            <td class="col-actions">
                                <div class="table-actions">

                                    <a href="{{ route('keuangan.invoice-layanan.show',$inv->id) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('keuangan.invoice-layanan.print',$inv->id) }}"
                                       class="btn btn-sm btn-outline-dark"
                                       title="Print">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <a href="{{ route('keuangan.payment-layanan.create',$inv->id) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Bayar">
                                        <i class="fas fa-wallet"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="table-empty">
                                Belum ada invoice layanan
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="card-footer">
            {{ $invoices->links() }}
        </div>
    </div>

    {{-- =====================================================
    FAB (MOBILE)
    ====================================================== --}}
    <div class="fab fab-desktop-hidden">
        <a href="{{ route('keuangan.transaksi-layanan.create') }}"
           class="fab-btn">
            <i class="fas fa-plus"></i>
        </a>
    </div>

</div>
@endsection
