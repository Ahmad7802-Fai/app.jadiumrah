@extends('layouts.admin')

@section('title', 'Transaksi Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h4 class="fw-bold mb-1">Transaksi Layanan</h4>
            <p class="text-muted small mb-0">
                Kelola transaksi pembelian layanan client
            </p>
        </div>
        <div class="page-action">
            <a href="{{ route('keuangan.transaksi-layanan.create') }}"
            class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Transaksi
            </a>
        </div>
    </div>

    {{-- =====================================================
        FILTER BAR
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-md-5">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control rounded-pill"
                           placeholder="Cari client / transaksi">
                </div>

                <div class="col-md-4">
                    <select name="status" class="form-control rounded-pill">
                        <option value="">Semua Status</option>
                        <option value="pending"  {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                        <option value="invoiced" {{ request('status')=='invoiced'?'selected':'' }}>Invoiced</option>
                        <option value="paid"     {{ request('status')=='paid'?'selected':'' }}>Paid</option>
                        <option value="cancelled"{{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100 rounded-pill">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- =====================================================
        TABLE
    ====================================================== --}}
    <div class="card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact mb-0">

                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="col-actions text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($transaksi as $t)
                        <tr>

                            <td data-label="Client">
                                <strong>{{ $t->client->nama ?? '-' }}</strong>
                            </td>

                            <td data-label="Item">
                                {{ $t->items_count ?? $t->items->count() }} item
                            </td>

                            <td data-label="Total" class="fw-bold text-success">
                                Rp {{ number_format($t->subtotal,0,',','.') }}
                            </td>

                            <td data-label="Status">
                                @if($t->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($t->status === 'invoiced')
                                    <span class="badge bg-info text-dark">Invoiced</span>
                                @elseif($t->status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>

                            <td data-label="Tanggal">
                                {{ $t->created_at->format('d M Y') }}
                            </td>

                            <td class="col-actions text-end" data-label="Aksi">
                                <div class="table-actions">

                                    <a href="{{ route('keuangan.transaksi-layanan.show',$t->id) }}"
                                       class="btn btn-sm btn-light"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('keuangan.transaksi-layanan.edit',$t->id) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if(!$t->invoice)
                                        <a href="{{ route('keuangan.invoice-layanan.generate',$t->id) }}"
                                           class="btn btn-sm btn-primary"
                                           title="Generate Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('keuangan.transaksi-layanan.destroy',$t->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Belum ada transaksi layanan
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            {!! $transaksi->links() !!}
        </div>
    </div>

    {{-- =====================================================
        FAB MOBILE
    ====================================================== --}}
    <div class="fab fab-desktop-hidden">
        <a href="{{ route('keuangan.transaksi-layanan.create') }}" class="fab-btn">
            <i class="fas fa-plus"></i>
        </a>
    </div>

</div>
@endsection
