@extends('layouts.admin')

@section('title', 'Approval Jamaah')

@section('content')
<div class="page-jamaah-approval">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Approval Jamaah</h1>
            <p class="text-muted text-sm">
                Proses persetujuan data jamaah
            </p>
        </div>
    </div>

    {{-- ===============================
       FLASH MESSAGE
    ================================ --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-times-circle me-1"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ===============================
       FILTER & SEARCH
    ================================ --}}
    <form method="GET" class="card card-hover mb-3">
        <div class="card-body">
            <input type="hidden" name="status" value="{{ $status }}">

            <div class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label text-sm">Cari Jamaah</label>
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Nama / No ID / Cabang"
                           value="{{ $search }}">
                </div>

                <div class="col-lg-auto d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>

                    @if($search)
                        <a href="{{ route('operator.jamaah-approval.index', ['status' => $status]) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- ===============================
       STATUS TABS
    ================================ --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}"
               href="{{ route('operator.jamaah-approval.index', ['status' => 'pending']) }}">
                🟡 Pending
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}"
               href="{{ route('operator.jamaah-approval.index', ['status' => 'approved']) }}">
                🟢 Approved
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}"
               href="{{ route('operator.jamaah-approval.index', ['status' => 'rejected']) }}">
                🔴 Rejected
            </a>
        </li>
    </ul>

    {{-- ===============================
       BULK APPROVE FORM
    ================================ --}}
    <form method="POST"
          action="{{ route('operator.jamaah-approval.bulk-approve') }}"
          onsubmit="return confirm('Approve semua jamaah terpilih?')">
        @csrf

        @if($status === 'pending' && $jamaahs->count())
            <div class="mb-2">
                <button class="btn btn-success btn-sm">
                    <i class="fas fa-check-double"></i>
                    Approve Terpilih
                </button>
            </div>
        @endif

        {{-- ===============================
           DESKTOP TABLE
        ================================ --}}
<div class="card card-hover">
    <div class="card-body p-0 table-responsive">

        <table class="table table-compact table-hover mb-0">

            <thead>
                <tr>
                    @if($status === 'pending')
                        <th width="40">
                            <input type="checkbox" id="check-all">
                        </th>
                    @endif
                    <th>No ID</th>
                    <th>Nama</th>
                    <th>Paket</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th class="table-right col-actions">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($jamaahs as $jamaah)
                <tr>

                    @if($status === 'pending')
                        <td data-label="Pilih">
                            <input type="checkbox"
                                   name="jamaah_ids[]"
                                   value="{{ $jamaah->id }}"
                                   class="check-item">
                        </td>
                    @endif

                    <td data-label="No ID">{{ $jamaah->no_id }}</td>

                    <td data-label="Nama" class="fw-semibold">
                        {{ $jamaah->nama_lengkap }}
                    </td>

                    <td data-label="Paket">
                        {{ $jamaah->nama_paket }}
                    </td>

                    <td data-label="Cabang">
                        {{ $jamaah->branch->nama_cabang ?? '-' }}
                    </td>

                    <td data-label="Status">
                        <span class="badge
                            {{ $jamaah->status === 'approved'
                                ? 'badge-soft-success'
                                : ($jamaah->status === 'rejected'
                                    ? 'badge-soft-danger'
                                    : 'badge-soft-warning') }}">
                            {{ strtoupper($jamaah->status) }}
                        </span>
                    </td>

                    <td data-label="Aksi" class="table-right col-actions">
                        @if($jamaah->status === 'pending')
                            <a href="{{ route('operator.jamaah-approval.show', $jamaah->id) }}"
                               class="btn btn-outline-primary btn-xs">
                                Detail
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        Tidak ada data jamaah
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>

    </div>
</div>

        {{-- ===============================
           PAGINATION
        ================================ --}}
        @if($jamaahs->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $jamaahs->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </form>
</div>

{{-- ===============================
   JS CHECK ALL
=============================== --}}
<script>
document.getElementById('check-all')?.addEventListener('change', function () {
    document.querySelectorAll('.check-item').forEach(cb => {
        cb.checked = this.checked
    })
})
</script>
@endsection
