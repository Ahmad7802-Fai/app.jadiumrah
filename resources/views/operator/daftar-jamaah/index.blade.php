@extends('layouts.admin')

@section('title', 'Daftar Jamaah')

@section('content')
<div class="page-jamaah">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Daftar Jamaah</h1>
            <p class="text-muted text-sm">
                Kelola data jamaah secara terpusat
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('operator.daftar-jamaah.export.pdf', request()->all()) }}"
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </a>

            <a href="{{ route('operator.daftar-jamaah.export.excel', request()->all()) }}"
               class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>

            <a href="{{ route('operator.daftar-jamaah.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i>
                Tambah Jamaah
            </a>
        </div>
    </div>

    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET" class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label text-sm">Cari Jamaah</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Nama, NIK, No ID">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-sm">Keberangkatan</label>
                    <select name="id_keberangkatan" class="form-select">
                        <option value="">Semua Keberangkatan</option>
                        @foreach($keberangkatanList as $k)
                            <option value="{{ $k->id }}"
                                @selected(request('id_keberangkatan') == $k->id)>
                                {{ $k->kode_keberangkatan }} —
                                {{ date('d M Y', strtotime($k->tanggal_berangkat)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-sm">Tipe Jamaah</label>
                    <select name="tipe_jamaah" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="reguler"  @selected(request('tipe_jamaah')=='reguler')>Reguler</option>
                        <option value="tabungan" @selected(request('tipe_jamaah')=='tabungan')>Tabungan</option>
                        <option value="cicilan"  @selected(request('tipe_jamaah')=='cicilan')>Cicilan</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('operator.daftar-jamaah.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    {{-- ===============================
       MOBILE — CARD LIST
    ================================ --}}
    <div class="d-md-none card-list">
        @forelse ($data as $j)
        <div class="card card-hover card-compact">
            <div class="card-body">

                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $j->foto ? asset('storage/'.$j->foto) : asset('noimage.jpg') }}"
                         class="avatar">

                    <div class="min-w-0">
                        <div class="fw-semibold text-truncate">
                            {{ $j->nama_lengkap }}
                        </div>
                        <div class="text-muted text-sm">
                            {{ $j->no_id }}
                        </div>
                    </div>
                </div>

                <div class="text-sm text-muted mt-2">
                    <div>
                        Keberangkatan:
                        <strong>
                            {{ $j->keberangkatan
                                ? date('d M Y', strtotime($j->keberangkatan->tanggal_berangkat))
                                : '-' }}
                        </strong>
                    </div>

                    <div>Paket: <strong>{{ $j->paket }}</strong></div>

                    @php
                        $tipeClass = match($j->tipe_jamaah) {
                            'reguler'  => 'badge-soft-primary',
                            'tabungan' => 'badge-soft-success',
                            'cicilan'  => 'badge-soft-warning',
                            default    => 'badge-soft-secondary',
                        };
                    @endphp

                    <div class="mt-1">
                        Tipe Jamaah:
                        <span class="badge {{ $tipeClass }} text-capitalize">
                            {{ $j->tipe_jamaah }}
                        </span>
                    </div>

                    <div class="mt-1">
                        Kamar:
                        <span class="badge badge-soft-primary">
                            {{ strtoupper($j->tipe_kamar) }}
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('operator.daftar-jamaah.show', $j->id) }}"
                       class="btn btn-outline-secondary btn-xs">
                        <i class="fas fa-eye"></i>
                    </a>

                    <a href="{{ route('operator.daftar-jamaah.edit', $j->id) }}"
                       class="btn btn-outline-warning btn-xs">
                        <i class="fas fa-edit"></i>
                    </a>

                    <button type="button"
                            class="btn btn-outline-danger btn-xs btn-delete"
                            data-id="{{ $j->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <form id="delete-form-{{ $j->id }}"
                      action="{{ route('operator.daftar-jamaah.destroy', $j->id) }}"
                      method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>

            </div>
        </div>
        @empty
            <div class="table-empty">
                Belum ada data jamaah
            </div>
        @endforelse
    </div>

    {{-- ===============================
       DESKTOP — TABLE
    ================================ --}}
    <div class="d-none d-md-block">
        <div class="card card-hover">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Keberangkatan</th>
                                <th>Paket</th>
                                <th>Tipe Jamaah</th>
                                <th>Kamar</th>
                                <th class="table-right col-actions">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse ($data as $j)
                            <tr>
                                <td>
                                    <img src="{{ $j->foto ? asset('storage/'.$j->foto) : asset('noimage.jpg') }}"
                                         class="avatar">
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $j->nama_lengkap }}</div>
                                    <div class="text-muted text-sm">{{ $j->no_id }}</div>
                                </td>

                                <td>{{ $j->nik }}</td>

                                <td {{ $j->keberangkatan ? '' : 'class=text-muted' }}>
                                    {{ $j->keberangkatan
                                        ? date('d M Y', strtotime($j->keberangkatan->tanggal_berangkat))
                                        : '-' }}
                                </td>

                                <td>{{ $j->paket }}</td>

                                @php
                                    $tipeClass = match($j->tipe_jamaah) {
                                        'reguler'  => 'badge badge-soft-primary',
                                        'tabungan' => 'badge badge-soft-success',
                                        'cicilan'  => 'badge badge-soft-warning',
                                        default    => 'badge-soft-secondary',
                                    };
                                @endphp

                                <td>
                                    <span class="badge {{ $tipeClass }} text-capitalize">
                                        {{ $j->tipe_jamaah }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge badge-soft-primary">
                                        {{ strtoupper($j->tipe_kamar) }}
                                    </span>
                                </td>

                                <td class="table-right">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('operator.daftar-jamaah.show', $j->id) }}"
                                           class="btn btn-outline-secondary btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('operator.daftar-jamaah.edit', $j->id) }}"
                                           class="btn btn-outline-warning btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-outline-danger btn-xs btn-delete"
                                                data-id="{{ $j->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $j->id }}"
                                          action="{{ route('operator.daftar-jamaah.destroy', $j->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="table-empty">
                                    Data jamaah belum tersedia
                                </td>
                            </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================
       PAGINATION
    ================================ --}}
    @if ($data->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $data->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- FAB MOBILE --}}
<div class="d-md-none">
    <x-fab.add route="{{ route('operator.daftar-jamaah.create') }}" />
</div>

{{-- DELETE CONFIRM --}}
@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id
        Swal.fire({
            title: 'Hapus Jamaah?',
            text: 'Data yang dihapus tidak dapat dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit()
            }
        })
    })
})
</script>
@endpush
@endsection
