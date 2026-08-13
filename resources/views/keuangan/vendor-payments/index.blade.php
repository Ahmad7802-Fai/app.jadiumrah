@extends('layouts.admin')

@section('title','Pembayaran Vendor')

@section('content')
<div class="page-container container-wide">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Pembayaran Vendor</h1>
            <p class="text-muted text-sm">
                Kelola pembayaran kepada vendor layanan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.vendor-payments.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Pembayaran
            </a>
        </div>
    </div>

    {{-- =====================================================
    FILTER
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label text-sm">Vendor</label>
                    <input type="text"
                           name="vendor"
                           value="{{ request('vendor') }}"
                           class="form-control"
                           placeholder="Nama vendor">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-sm">Tanggal</label>
                    <input type="date"
                           name="tanggal"
                           value="{{ request('tanggal') }}"
                           class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-sm">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="paid"     @selected(request('status')=='paid')>Paid</option>
                        <option value="pending"  @selected(request('status')=='pending')>Pending</option>
                        <option value="rejected" @selected(request('status')=='rejected')>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
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
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor</th>
                            <th>Item</th>
                            <th>No Invoice</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($payments as $i => $p)
                        <tr>
                            <td data-label="#"> {{ $payments->firstItem() + $i }} </td>

                            <td data-label="Vendor">
                                <div class="fw-semibold">{{ $p->vendor_name }}</div>
                            </td>

                            <td data-label="Item">
                                {{ $p->layananItem->nama_item ?? '-' }}
                            </td>

                            <td data-label="Invoice">
                                {{ $p->invoice_number ?? '-' }}
                            </td>

                            <td data-label="Jumlah" class="fw-semibold text-success">
                                Rp {{ number_format($p->amount,0,',','.') }}
                            </td>

                            <td data-label="Tanggal">
                                {{ date('d M Y',strtotime($p->payment_date)) }}
                            </td>

                            <td data-label="Status">
                                <span class="badge bg-info text-dark">
                                    Recorded
                                </span>
                            </td>

                            <td class="table-right col-actions">
                                <div class="table-actions">
                                    <a href="{{ route('keuangan.vendor-payments.show',$p->id) }}"
                                       class="btn btn-xs btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('keuangan.vendor-payments.edit',$p->id) }}"
                                       class="btn btn-xs btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('keuangan.vendor-payments.destroy',$p->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus pembayaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-empty">
                                Belum ada pembayaran vendor
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>

</div>

{{-- =====================================================
FAB MOBILE
===================================================== --}}
<div class="fab d-md-none">
    <a href="{{ route('keuangan.vendor-payments.create') }}"
       class="fab-btn">
        <i class="fas fa-plus"></i>
    </a>
</div>

@endsection
