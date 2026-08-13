@extends('layouts.admin')

@section('title','Data Manifest Jamaah')

@section('content')
<div class="page-manifest">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Manifest Jamaah</h1>
            <p class="page-subtitle">
                Kelola kamar & jamaah keberangkatan
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions">

            @if(request('keberangkatan_id'))
                <a href="{{ route('operator.manifest.create', ['keberangkatan_id'=>request('keberangkatan_id')]) }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Tambah Manifest
                </a>

                <a href="{{ route('operator.manifest.print', request('keberangkatan_id')) }}"
                   class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf"></i>
                    Print Manifest
                </a>
            @else
                <button class="btn btn-primary btn-sm" disabled>
                    <i class="fas fa-plus"></i>
                    Tambah Manifest
                </button>

                <button class="btn btn-outline-danger btn-sm" disabled>
                    <i class="fas fa-file-pdf"></i>
                    Print Manifest
                </button>
            @endif

        </div>
    </div>

    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET"
          action="{{ route('operator.manifest.index') }}"
          class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label text-sm">Keberangkatan</label>
                    <select name="keberangkatan_id" class="form-select">
                        <option value="">Semua Keberangkatan</option>
                        @foreach($keberangkatanList as $k)
                            <option value="{{ $k->id }}"
                                @selected(request('keberangkatan_id')==$k->id)>
                                {{ $k->kode_keberangkatan }}
                                ({{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-sm">Cari Jamaah</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Nama jamaah / nomor kamar">
                </div>

                <div class="col-md-2 col-6">
                    <button class="btn btn-primary w-100 btn-sm">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                </div>

                <div class="col-md-2 col-6">
                    <a href="{{ route('operator.manifest.index') }}"
                       class="btn btn-outline-secondary w-100 btn-sm">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    {{-- ===============================
       STAT CARDS
    ================================ --}}
    <div class="row mb-3">
        <div class="col-md-4 col-12 mb-2">
            <div class="card card-soft text-center">
                <div class="card-body py-3">
                    <div class="text-sm text-muted">Total Jamaah</div>
                    <div class="fs-4 fw-bold text-primary">{{ $stat_total }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12 mb-2">
            <div class="card card-soft text-center">
                <div class="card-body py-3">
                    <div class="text-sm text-muted">Total Kamar</div>
                    <div class="fs-4 fw-bold text-success">{{ $stat_kamar }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12 mb-2">
            <div class="card card-soft text-center">
                <div class="card-body py-3 text-sm">
                    <div class="fw-semibold mb-1">Tipe Kamar</div>
                    Quad: <b>{{ $stat_quad }}</b> ·
                    Triple: <b>{{ $stat_triple }}</b> ·
                    Double: <b>{{ $stat_double }}</b>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================
       TABLE
    ================================ --}}
    <div class="card card-hover">
        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-compact align-middle">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Nama Jamaah</th>
                            <th class="table-center">Tipe</th>
                            <th class="table-center">No Kamar</th>
                            <th>Keberangkatan</th>
                            <th class="table-center col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($manifests as $m)
                        <tr>
                            <td>
                                {{ $loop->iteration + ($manifests->currentPage()-1)*$manifests->perPage() }}
                            </td>

                            <td>{{ $m->jamaah->nama_lengkap }}</td>

                            <td class="table-center">
                                <span class="badge badge-soft-primary">
                                    {{ $m->tipe_kamar }}
                                </span>
                            </td>

                            <td class="table-center fw-semibold">
                                {{ $m->nomor_kamar }}
                            </td>

                            <td>
                                {{ $m->keberangkatan->kode_keberangkatan ?? '-' }}
                                <div class="text-muted text-sm">
                                    {{ optional($m->keberangkatan)->tanggal_berangkat
                                        ? \Carbon\Carbon::parse($m->keberangkatan->tanggal_berangkat)->format('d M Y')
                                        : '-' }}
                                </div>
                            </td>

                            <td class="table-center col-actions">
                                <a href="{{ route('operator.manifest.edit', $m->id) }}"
                                   class="btn btn-outline-primary btn-xs">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('operator.manifest.destroy', $m->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Hapus data manifest ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-xs">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Tidak ada data manifest
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $manifests->withQueryString()->links() }}
    </div>

</div>

{{-- ===============================
   FAB — MOBILE ONLY
=============================== --}}
@if(request('keberangkatan_id'))
<div class="d-md-none">
    <div id="fab-wrapper" class="fab-wrapper">
        <a href="{{ route('operator.manifest.print', request('keberangkatan_id')) }}"
           class="fab-mini fab-danger">
            <i class="fas fa-file-pdf"></i>
        </a>

        <a href="{{ route('operator.manifest.create', ['keberangkatan_id'=>request('keberangkatan_id')]) }}"
           class="fab-mini fab-primary">
            <i class="fas fa-plus"></i>
        </a>

        <button id="fab-main" class="fab-main">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
@endif
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const wrap = document.getElementById('fab-wrapper')
    const main = document.getElementById('fab-main')

    if (wrap && main) {
        main.addEventListener('click', () => {
            wrap.classList.toggle('fab-open')
            main.style.transform =
                wrap.classList.contains('fab-open')
                    ? 'rotate(45deg)'
                    : 'rotate(0deg)'
        })
    }

})
</script>
@endpush
