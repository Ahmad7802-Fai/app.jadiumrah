@extends('layouts.app')

@section('title', 'Invoice Layanan')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Invoice Layanan</h4>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>No Invoice</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoices as $inv)

                    <tr>
                        <td><strong>{{ $inv->no_invoice }}</strong></td>

                        <td>
                            {{ $inv->transaksi->client->nama }}
                        </td>

                        <td>Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>

                        <td>Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>

                        <td>
                            @if($inv->status == 'unpaid')
                                <span class="badge bg-danger">Unpaid</span>
                            @elseif($inv->status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @elseif($inv->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </td>

                        <td>
                            {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : '-' }}
                        </td>

                        <td>
                            <a href="{{ route('layanan.invoice.show', $inv->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>

                            <a href="{{ route('layanan.invoice.print', $inv->id) }}"
                               class="btn btn-sm btn-outline-dark">
                                Print
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-4 text-muted">
                            Tidak ada invoice.
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
