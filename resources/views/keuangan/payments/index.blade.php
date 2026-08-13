@extends('layouts.admin')

@section('title','Pembayaran Jamaah')

@section('content')

<div class="page-pembayaran">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Pembayaran Jamaah</h1>
            <p class="text-muted text-sm">
                Semua pembayaran agent & cabang wajib divalidasi oleh keuangan
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.payments.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Tambah Pembayaran
            </a>
        </div>
    </div>
    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Cari jamaah / ID / invoice">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending"  @selected(request('status')=='pending')>Pending</option>
                        <option value="valid"    @selected(request('status')=='valid')>Valid</option>
                        <option value="ditolak"  @selected(request('status')=='ditolak')>Ditolak</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===============================
       DESKTOP TABLE
    ================================ --}}
    <div class="card shadow-sm d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Jamaah</th>
                    <th>No Invoice</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($payments as $i => $p)
                <tr>
                    <td>{{ $payments->firstItem() + $i }}</td>

                    <td>
                        @if($p->jamaah)
                            <strong>{{ $p->jamaah->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $p->jamaah->no_id }}</small>
                        @else
                            <strong class="text-muted">Non Jamaah</strong><br>
                            <small class="text-muted">—</small>
                        @endif
                    </td>


                    <td>{{ $p->invoice->nomor_invoice ?? 'Menunggu Invoice' }}</td>
                    <td>{{ strtoupper($p->metode) }}</td>
                    <td>Rp {{ number_format($p->jumlah) }}</td>

                    <td>
                        @if($p->status === 'pending')
                            <span class="badge badge-soft-primary">Pending</span>
                        @elseif($p->status === 'valid')
                            <span class="badge badge-soft-success">Valid</span>
                        @else
                            <span class="badge badge-soft-danger">Ditolak</span>
                        @endif
                    </td>

                    <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>

                    <td class="text-end">
                        <div class="btn-group btn-group-sm">

                            @if($p->status === 'pending')
                                <button class="btn btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal"
                                        data-id="{{ $p->id }}"
                                        data-name="{{ $p->jamaah->nama_lengkap }}"
                                        data-amount="{{ number_format($p->jumlah) }}">
                                    <i class="fas fa-check"></i>
                                </button>

                                <button class="btn btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal"
                                        data-id="{{ $p->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif

                            <a href="{{ route('keuangan.payments.show',$p->id) }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($p->status === 'valid')
                                <a href="{{ route('keuangan.payments.kwitansi.premium',$p->id) }}"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>

    {{-- ===============================
       MOBILE CARDS
    ================================ --}}
    <div class="d-md-none">
        @foreach($payments as $p)
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <strong>
                    {{ $p->jamaah?->nama_lengkap ?? 'Non Jamaah' }}
                </strong>
                <div class="text-muted small">
                    {{ $p->jamaah?->no_id ?? '—' }}
                </div>

                <div class="mt-2">
                    <strong>Rp {{ number_format($p->jumlah) }}</strong><br>
                    <small class="text-muted">
                        {{ $p->invoice->nomor_invoice ?? 'Menunggu Invoice' }}
                    </small>
                </div>

                <div class="mt-2">
                    @if($p->status === 'pending')
                        <span class="badge badge-soft-primary">Pending</span>
                    @elseif($p->status === 'valid')
                        <span class="badge badge-soft-success">Valid</span>
                    @else
                        <span class="badge badge-soft-danger">Ditolak</span>
                    @endif
                </div>

                @if($p->status === 'pending')
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-success w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#approveModal"
                            data-id="{{ $p->id }}"
                            data-name="{{ $p->jamaah->nama_lengkap }}"
                            data-amount="{{ number_format($p->jumlah) }}">
                        Approve
                    </button>

                    <button class="btn btn-danger w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal"
                            data-id="{{ $p->id }}">
                        Reject
                    </button>
                </div>
                @endif

            </div>
        </div>
        @endforeach

        {{ $payments->links() }}
    </div>

</div>

{{-- ===============================
   APPROVE MODAL
=============================== --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="approveForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Approve Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p id="approveText"></p>
                <small class="text-muted">
                    Invoice akan dibuat otomatis jika belum ada
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success">Approve</button>
            </div>
        </form>
    </div>
</div>

{{-- ===============================
   REJECT MODAL
=============================== --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <textarea name="reason"
                          class="form-control"
                          rows="3"
                          required
                          placeholder="Alasan penolakan"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('approveModal')
?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget
    const id = btn.dataset.id
    const name = btn.dataset.name
    const amount = btn.dataset.amount

    document.getElementById('approveText').innerHTML =
        `Approve pembayaran <strong>Rp ${amount}</strong> atas nama <strong>${name}</strong>?`

    document.getElementById('approveForm').action =
        `/keuangan/pembayaran/${id}/approve`
})

document.getElementById('rejectModal')
?.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget
    const id = btn.dataset.id

    document.getElementById('rejectForm').action =
        `/keuangan/pembayaran/${id}/reject`
})
</script>
@endpush

{{-- @extends('layouts.admin')

@section('title','Pembayaran Jamaah')

@section('content')
<div class="page-container"> --}}

    {{-- =========================================================
    | HEADER
    ========================================================= --}}
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Pembayaran Jamaah</h4>
            <small class="text-muted">
                Semua pembayaran agent & cabang wajib divalidasi oleh keuangan
            </small>
        </div>

        <a href="{{ route('keuangan.payments.create') }}"
           class="btn-ju d-none d-md-inline-flex">
            <i class="fas fa-plus me-2"></i> Tambah Pembayaran
        </a>
    </div> --}}

    {{-- =========================================================
    | FILTER
    ========================================================= --}}
    {{-- <form method="GET" class="card-premium p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q"
                       value="{{ request('q') }}"
                       class="form-control rounded-pill"
                       placeholder="Cari jamaah / ID / invoice">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-control rounded-pill">
                    <option value="">Semua Status</option>
                    <option value="pending" @selected(request('status')=='pending')>Pending</option>
                    <option value="valid" @selected(request('status')=='valid')>Valid</option>
                    <option value="ditolak" @selected(request('status')=='ditolak')>Ditolak</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn-ju w-100 rounded-pill">
                    <i class="fas fa-search me-2"></i> Filter
                </button>
            </div>
        </div>
    </form> --}}

    {{-- =========================================================
    | DESKTOP TABLE
    ========================================================= --}}
    {{-- <div class="card-premium d-none d-md-block">
        <div class="table-responsive-premium">
            <table class="table-premium mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Jamaah</th>
                    <th>No Invoice</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>

                <tbody>
                @foreach($payments as $i => $p)
                <tr>
                    <td>{{ $payments->firstItem() + $i }}</td>

                    <td>
                        <strong>{{ $p->jamaah->nama_lengkap }}</strong><br>
                        <small class="text-muted">{{ $p->jamaah->no_id }}</small>
                    </td>

                    <td>
                        {{ $p->invoice->nomor_invoice ?? 'Menunggu Invoice' }}
                    </td>

                    <td>{{ strtoupper($p->metode) }}</td>

                    <td>Rp {{ number_format($p->jumlah) }}</td>

                    <td>
                        @if($p->status === 'pending')
                            <span class="badge-ju-warning-pill">PENDING</span>
                        @elseif($p->status === 'valid')
                            <span class="badge-ju-success-pill">VALID</span>
                        @else
                            <span class="badge-ju-danger-pill">DITOLAK</span>
                        @endif
                    </td>

                    <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>

                    <td class="text-end">
                        <div class="action-buttons">

                            @if($p->status === 'pending')
                            <button class="btn-action-success"
                                data-bs-toggle="modal"
                                data-bs-target="#approveModal"
                                data-id="{{ $p->id }}"
                                data-name="{{ $p->jamaah->nama_lengkap }}"
                                data-amount="{{ number_format($p->jumlah) }}">
                                <i class="fas fa-check"></i>
                            </button>

                            <button class="btn-action-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#rejectModal"
                                data-id="{{ $p->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif

                            <a href="{{ route('keuangan.payments.show',$p->id) }}"
                               class="btn-action-info">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($p->status === 'valid')
                            <a href="{{ route('keuangan.payments.kwitansi.premium',$p->id) }}"
                               class="btn-action-soft">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $payments->links() }}
        </div>
    </div> --}}

    {{-- =========================================================
    | MOBILE CARDS
    ========================================================= --}}
    {{-- <div class="d-md-none">
        @foreach($payments as $p)
        <div class="card-premium mb-3 p-3">

            <strong>{{ $p->jamaah->nama_lengkap }}</strong>
            <small class="text-muted d-block">{{ $p->jamaah->no_id }}</small>

            <div class="mt-2">
                <strong>Rp {{ number_format($p->jumlah) }}</strong><br>
                <small class="text-muted">
                    {{ $p->invoice->nomor_invoice ?? 'Menunggu Invoice' }}
                </small>
            </div>

            <div class="mt-2">
                @if($p->status === 'pending')
                    <span class="badge-ju-warning-pill">PENDING</span>
                @elseif($p->status === 'valid')
                    <span class="badge-ju-success-pill">VALID</span>
                @else
                    <span class="badge-ju-danger-pill">DITOLAK</span>
                @endif
            </div>

            @if($p->status === 'pending')
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-success w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#approveModal"
                    data-id="{{ $p->id }}"
                    data-name="{{ $p->jamaah->nama_lengkap }}"
                    data-amount="{{ number_format($p->jumlah) }}">
                    Approve
                </button>

                <button class="btn btn-danger w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal"
                    data-id="{{ $p->id }}">
                    Reject
                </button>
            </div>
            @endif

        </div>
        @endforeach

        {{ $payments->links() }}
    </div>

</div> --}}

{{-- =========================================================
| APPROVE MODAL (GLOBAL)
========================================================= --}}
{{-- <div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="approveForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Approve Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p id="approveText"></p>
                <small class="text-muted">
                    Invoice akan dibuat otomatis jika belum ada
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success">Approve</button>
            </div>
        </form>
    </div>
</div> --}}

{{-- =========================================================
| REJECT MODAL (GLOBAL)
========================================================= --}}
{{-- <div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <textarea name="reason"
                          class="form-control"
                          rows="3"
                          required
                          placeholder="Alasan penolakan"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

@endsection --}}

{{-- @push('scripts')
<script>
document.getElementById('approveModal')
.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget
    const id = btn.dataset.id
    const name = btn.dataset.name
    const amount = btn.dataset.amount

    document.getElementById('approveText').innerHTML =
        `Approve pembayaran <strong>Rp ${amount}</strong> atas nama <strong>${name}</strong>?`

    // ✅ FIX DI SINI
    document.getElementById('approveForm').action =
        `/keuangan/pembayaran/${id}/approve`
})

document.getElementById('rejectModal')
.addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget
    const id = btn.dataset.id

    document.getElementById('rejectForm').action =
        `/keuangan/pembayaran/${id}/reject`
})
</script>
@endpush --}}

{{-- @extends('layouts.admin')

@section('title', 'Pembayaran Jamaah')

@section('content')
<div class="page-container">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Pembayaran Jamaah</h4>
            <small class="text-muted">Kelola pembayaran, validasi dan invoice jamaah.</small>
        </div> --}}

        {{-- DESKTOP BUTTON --}}
        {{-- <a href="{{ route('keuangan.payments.create') }}" 
           class="btn-ju d-none d-md-inline-flex">
            <i class="fas fa-plus me-2"></i> Tambah Pembayaran
        </a>
    </div> --}}


    {{-- ========================================================= --}}
    {{-- FILTER BAR --}}
    {{-- ========================================================= --}}
    {{-- <form method="GET" action="" class="card-premium p-3 mb-4">
        <div class="row g-2"> --}}

            {{-- Search --}}
            {{-- <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}"
                       class="form-control rounded-pill"
                       placeholder="Cari nama, nomor ID, invoice...">
            </div> --}}

            {{-- Status --}}
            {{-- <div class="col-md-3">
                <select name="status" class="form-control rounded-pill">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
                    <option value="valid" {{ request('status')=='valid' ? 'selected':'' }}>Valid</option>
                </select>
            </div> --}}

            {{-- Submit --}}
            {{-- <div class="col-md-3">
                <button class="btn-ju w-100 rounded-pill">
                    <i class="fas fa-search me-2"></i> Cari
                </button>
            </div>

        </div>
    </form> --}}


    {{-- ========================================================= --}}
    {{-- DESKTOP TABLE VIEW --}}
    {{-- ========================================================= --}}
    {{-- <div class="card-premium p-0 d-none d-md-block">
        <div class="table-responsive-premium">
            <table class="table-premium mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jamaah</th>
                        <th>No Invoice</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($payments as $i => $p)
                    <tr>
                        <td>{{ $payments->firstItem() + $i }}</td>

                        <td>
                            <strong>{{ $p->jamaah->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $p->jamaah->no_id }}</small>
                        </td>

                        <td>
                            @if($p->invoice)
                                {{ $p->invoice->nomor_invoice }}
                            @else
                                <span class="badge-ju-warning-pill">PENDING INVOICE</span>
                            @endif
                        </td>


                        <td>
                            <span class="badge-ju-soft">{{ strtoupper($p->metode) }}</span>
                        </td>

                        <td>Rp {{ number_format($p->jumlah) }}</td>

                        <td>
                            @if($p->status == 'pending')
                                <span class="badge-ju-warning-pill">PENDING</span>
                            @else
                                <span class="badge-ju-success-pill">VALID</span>
                            @endif
                        </td>

                        <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}</td>

                        <td class="text-end">
                            <div class="action-buttons"> --}}

                                {{-- VALIDATE --}}
                                {{-- @if($p->status == 'pending')
                                <form action="{{ route('keuangan.payments.validate', $p->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn-action-success" title="Validasi">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif --}}

                                {{-- KWITANSI
                                <a href="{{ route('keuangan.payments.kwitansi.premium', $p->id) }}"
                                   class="btn-action-soft" title="Kwitansi">
                                    <i class="fas fa-file-invoice"></i>
                                </a> --}}

                                {{-- SHOW --}}
                                {{-- <a href="{{ route('keuangan.payments.show', $p->id) }}"
                                   class="btn-action-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a> --}}

                                {{-- EDIT --}}
                                {{-- <a href="{{ route('keuangan.payments.edit', $p->id) }}"
                                   class="btn-action-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a> --}}

                                {{-- DELETE --}}
                                {{-- <form action="{{ route('keuangan.payments.destroy', $p->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus pembayaran ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-action-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        <div class="p-3">
            {!! $payments->links() !!}
        </div>
    </div> --}}


    {{-- ========================================================= --}}
    {{-- MOBILE CARD VIEW --}}
    {{-- ========================================================= --}}
    {{-- <div class="d-md-none">

        @foreach($payments as $p)
        <div class="card-premium mb-3 p-3">

            <div class="fw-bold">{{ $p->jamaah->nama_lengkap }}</div>
            <small class="text-muted">{{ $p->jamaah->no_id }}</small>

            <div class="mt-2">
                <strong>Rp {{ number_format($p->jumlah) }}</strong><br>
                <small class="text-muted">
                    {{ $p->invoice->nomor_invoice ?? 'Menunggu invoice' }}
                </small>

                @if($p->status == 'pending')
                    <span class="badge-ju-warning-pill">PENDING</span>
                @else
                    <span class="badge-ju-success-pill">VALID</span>
                @endif
            </div>

            <div class="mt-3 d-flex gap-2"> --}}

                {{-- @if($p->status == 'pending')
                <form action="{{ route('keuangan.payments.validate', $p->id) }}"
                      method="POST" class="flex-grow-1">
                    @csrf
                    <button class="btn-action-success w-100">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif --}}

                {{-- <a href="{{ route('keuangan.payments.kwitansi.premium', $p->id) }}"
                   class="btn-action-soft flex-grow-1">
                    <i class="fas fa-file-invoice"></i>
                </a>

                <a href="{{ route('keuangan.payments.show', $p->id) }}"
                   class="btn-action-info flex-grow-1">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('keuangan.payments.edit', $p->id) }}"
                   class="btn-action-warning flex-grow-1">
                    <i class="fas fa-edit"></i>
                </a>

                <form action="{{ route('keuangan.payments.destroy', $p->id) }}"
                      method="POST"
                      class="flex-grow-1"
                      onsubmit="return confirm('Hapus pembayaran ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-action-danger w-100">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>

            </div>

        </div>
        @endforeach

        {!! $payments->links() !!} --}}

        {{-- Floating Add Button --}}
        {{-- <a href="{{ route('keuangan.payments.create') }}" class="fab-ju">
            <i class="fas fa-plus"></i>
        </a>
    </div>

</div>
@endsection --}}
