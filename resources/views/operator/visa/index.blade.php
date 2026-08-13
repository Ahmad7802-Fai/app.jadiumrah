@extends('layouts.admin')

@section('title','Data Visa Jamaah')

@section('content')
<div class="page-visa">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Visa Jamaah</h1>
            <p class="page-subtitle">
                Pengurusan visa jamaah berdasarkan keberangkatan
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions">
            <a href="{{ route('operator.visa.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Visa
            </a>
        </div>
    </div>

    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET"
          action="{{ route('operator.visa.index') }}"
          class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                {{-- KEBERANGKATAN --}}
                <div class="col-md-3">
                    <label class="form-label text-sm">Keberangkatan</label>
                    <select name="keberangkatan_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach($keberangkatan as $k)
                            <option value="{{ $k->id }}"
                                @selected(request('keberangkatan_id') == $k->id)>
                                {{ $k->kode_keberangkatan }}
                                ({{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SEARCH --}}
                <div class="col-md-4">
                    <label class="form-label text-sm">Cari</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Nama jamaah / nomor visa">
                </div>

                {{-- ACTION --}}
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('operator.visa.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    {{-- ===============================
       TABLE
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-compact align-middle">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Jamaah</th>
                            <th>Keberangkatan</th>
                            <th class="table-center">Status</th>
                            <th class="table-center">Nomor Visa</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($visas as $v)
                        <tr>

                            <td>
                                {{ $loop->iteration + ($visas->currentPage()-1)*$visas->perPage() }}
                            </td>

                            {{-- JAMAAH --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $v->jamaah->nama_lengkap ?? '-' }}
                                </div>
                                <div class="text-muted text-sm">
                                    {{ $v->jamaah->no_id ?? '-' }}
                                </div>
                            </td>

                            {{-- KEBERANGKATAN --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $v->keberangkatan->kode_keberangkatan }}
                                </div>
                                <div class="text-muted text-sm">
                                    {{ \Carbon\Carbon::parse($v->keberangkatan->tanggal_berangkat)->format('d M Y') }}
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="table-center">
                                @php
                                    $badge = match($v->status) {
                                        'Approved' => 'badge-soft-success',
                                        'Proses'   => 'badge-soft-warning',
                                        default    => 'badge-soft-danger'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">
                                    {{ strtoupper($v->status) }}
                                </span>
                            </td>

                            {{-- NOMOR VISA --}}
                            <td class="table-center">
                                {{ $v->nomor_visa ?? '-' }}
                            </td>

                            {{-- ACTION --}}
                            <td class="table-right col-actions">
                                <div class="table-actions">

                                    <a href="{{ route('operator.visa.edit', $v->id) }}"
                                       class="btn btn-outline-primary btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('operator.visa.destroy', $v->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus data visa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Belum ada data visa
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>

            </div>
        </div>
    </div>

    {{-- ===============================
       PAGINATION
    ================================ --}}
    @if($visas->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $visas->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- ===============================
   FAB — MOBILE ONLY
=============================== --}}
<div id="fab-wrapper" class="d-md-none">

    {{-- RESET --}}
    <a href="{{ route('operator.visa.index') }}"
       id="fab-reset"
       class="fab-mini">
        <i class="fas fa-undo"></i>
    </a>
    <div class="fab-label">Reset Filter</div>

    {{-- ADD VISA --}}
    <a href="{{ route('operator.visa.create') }}"
       id="fab-add"
       class="fab-mini">
        <i class="fas fa-plus"></i>
    </a>
    <div class="fab-label">Tambah Visa</div>

    {{-- MAIN --}}
    <div id="fab-main">
        <i class="fas fa-plus"></i>
    </div>
</div>

@endsection
