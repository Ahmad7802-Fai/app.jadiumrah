@extends('layouts.app')

@section('title', 'Transaksi Layanan')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Transaksi Layanan</h4>
        <a href="{{ route('layanan.transaksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Transaksi Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksi as $trx)
                    <tr>
                        <td>{{ $trx->id }}</td>

                        <td>
                            <strong>{{ $trx->client->nama }}</strong><br>
                            <small class="text-muted">{{ $trx->client->email }}</small>
                        </td>

                        <td>
                            Rp {{ number_format($trx->subtotal, 0, ',', '.') }}
                        </td>

                        <td>
                            @if($trx->status == 'pending')
                                <span class="badge bg-secondary">Pending</span>
                            @elseif($trx->status == 'invoiced')
                                <span class="badge bg-info text-dark">Invoiced</span>
                            @elseif($trx->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($trx->status == 'canceled')
                                <span class="badge bg-danger">Canceled</span>
                            @endif
                        </td>

                        <td>{{ $trx->created_at->format('d M Y') }}</td>

                        <td>
                            <a href="{{ route('layanan.transaksi.show', $trx->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>

                            @if($trx->status == 'pending')
                                <a href="{{ route('layanan.invoice.create', ['layanan_id' => $trx->id]) }}"
                                   class="btn btn-sm btn-outline-dark">
                                    Invoice
                                </a>
                            @endif
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4 text-muted">
                            Tidak ada transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
