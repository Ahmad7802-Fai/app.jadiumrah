@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Data Pembayaran Jamaah</h4>
        <a href="{{ route('keuangan.payments.create') }}" class="btn btn-primary rounded-pill px-4 d-none d-md-inline">
            + Tambah Pembayaran
        </a>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">

                {{-- SEARCH --}}
                <div class="col-md-4">
                    <label class="form-label">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" 
                        class="form-control" placeholder="Nama, No ID, NIK, Invoice, Keterangan...">
                </div>

                {{-- STATUS --}}
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
                        <option value="valid" {{ request('status')=='valid' ? 'selected':'' }}>Valid</option>
                        <option value="ditolak" {{ request('status')=='ditolak' ? 'selected':'' }}>Ditolak</option>
                    </select>
                </div>

                {{-- METODE --}}
                <div class="col-md-2">
                    <label class="form-label">Metode</label>
                    <select name="metode" class="form-control">
                        <option value="">Semua</option>
                        <option value="transfer" {{ request('metode')=='transfer'?'selected':'' }}>Transfer</option>
                        <option value="cash" {{ request('metode')=='cash'?'selected':'' }}>Cash</option>
                        <option value="kantor" {{ request('metode')=='kantor'?'selected':'' }}>Kantor</option>
                        <option value="gateway" {{ request('metode')=='gateway'?'selected':'' }}>Gateway</option>
                    </select>
                </div>

                {{-- KEBERANGKATAN --}}
                <div class="col-md-3">
                    <label class="form-label">Keberangkatan</label>
                    <select name="keberangkatan_id" class="form-control">
                        <option value="">Semua</option>
                        @foreach ($keberangkatan as $k)
                            <option value="{{ $k->id }}" 
                                {{ request('keberangkatan_id')==$k->id?'selected':'' }}>
                                {{ $k->kode_keberangkatan }} ({{ $k->tanggal_berangkat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FILTER BUTTON --}}
                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-success w-100">Cari</button>
                </div>

            </form>
        </div>
    </div>


    {{-- TABEL PEMBAYARAN --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Jamaah</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($payments as $p)
                        <tr>
                            <td>{{ $p->id }}</td>

                            <td>
                                {{ $p->jamaah->nama_lengkap ?? '-' }}<br>
                                <small class="text-muted">{{ $p->jamaah->no_id ?? '-' }}</small>
                            </td>

                            <td>
                                <strong>{{ $p->invoice->nomor_invoice ?? '-' }}</strong>
                            </td>

                            <td>{{ $p->tanggal_bayar }}</td>

                            <td>{{ ucfirst($p->metode) }}</td>

                            <td>Rp {{ number_format($p->jumlah) }}</td>

                            <td>
                                @if($p->status=='valid')
                                    <span class="badge badge-success">Valid</span>
                                @elseif($p->status=='pending')
                                    <span class="badge badge-warning text-dark">Pending</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('keuangan.payments.show', $p->id) }}" 
                                    class="btn btn-sm btn-light">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('keuangan.payments.edit', $p->id) }}" 
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('keuangan.payments.destroy', $p->id) }}" 
                                    method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Hapus pembayaran ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Tidak ada pembayaran.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="mt-3">
        {{ $payments->links() }}
    </div>


    {{-- FAB BUTTON MOBILE --}}
    <button onclick="window.location='{{ route('keuangan.payments.create') }}'"
        class="fab-create d-md-none">
        +
    </button>

</div>
@endsection


@push('styles')
<style>
@media (max-width: 768px) {
    .fab-create {
        position: fixed;
        bottom: 25px;
        right: 20px;
        z-index: 9999;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #28a745;
        color: #fff;
        font-size: 32px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }
}
</style>
@endpush
