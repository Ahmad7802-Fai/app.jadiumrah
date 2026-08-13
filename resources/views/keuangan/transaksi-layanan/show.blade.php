@extends('layouts.admin')

@section('title','Detail Transaksi Layanan')

@section('content')
<div class="page-container page-container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Detail Transaksi Layanan</h1>
            <p class="text-muted text-sm">
                Informasi client & layanan yang digunakan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.transaksi-layanan.index') }}"
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>

            <a href="{{ route('keuangan.transaksi-layanan.edit',$trx->id) }}"
               class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>

            <form action="{{ route('keuangan.transaksi-layanan.destroy',$trx->id) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin hapus transaksi ini?')"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- =====================================================
    CLIENT INFO
    ====================================================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informasi Client</h3>
        </div>

        <div class="card-body card-body-lg">
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <div class="text-muted text-sm">Client</div>
                    <div class="fw-bold fs-5">
                        {{ $trx->client->nama }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted text-sm">Catatan</div>
                    <div>
                        {{ $trx->notes ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="text-muted text-sm">
                Tanggal Input:
                {{ $trx->created_at->format('d M Y H:i') }}
                &nbsp;•&nbsp;
                Status:
                <strong class="text-capitalize">
                    {{ $trx->status }}
                </strong>
            </div>
        </div>
    </div>

    {{-- =====================================================
    ITEM LAYANAN
    ====================================================== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Item Layanan</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th width="80">Qty</th>
                            <th width="80">Hari</th>
                            <th width="160">Harga</th>
                            <th width="160" class="table-right">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($trx->items as $it)
                        <tr>
                            <td data-label="Item">
                                {{ $it->item->nama_item }}
                            </td>

                            <td data-label="Qty">
                                {{ $it->qty }}
                            </td>

                            <td data-label="Hari">
                                {{ $it->days }}
                            </td>

                            <td data-label="Harga">
                                Rp {{ number_format($it->harga,0,',','.') }}
                            </td>

                            <td data-label="Subtotal" class="table-right fw-semibold">
                                Rp {{ number_format($it->subtotal,0,',','.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table-empty">
                                Tidak ada item layanan
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <div class="fw-bold fs-5">
                    Total:
                    <span class="text-primary">
                        Rp {{ number_format($trx->subtotal,0,',','.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
    INVOICE ACTION
    ====================================================== --}}
    <div class="card">
        <div class="card-body text-center card-body-lg">
            @if($trx->invoice)
                <a href="{{ route('keuangan.invoice-layanan.show',$trx->invoice->id) }}"
                   class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-file-invoice me-2"></i>
                    Lihat Invoice
                </a>
            @else
                <a href="{{ route('keuangan.invoice-layanan.generate',$trx->id) }}"
                   class="btn btn-success btn-lg px-5">
                    <i class="fas fa-bolt me-2"></i>
                    Generate Invoice
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
