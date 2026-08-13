@extends('layouts.admin')

@section('title','Keberangkatan')

@section('content')
<div class="page-keberangkatan">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Keberangkatan</h1>
            <p class="page-subtitle">
                Kelola jadwal keberangkatan umrah
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions">
            <a href="{{ route('operator.keberangkatan.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Keberangkatan
            </a>
        </div>
    </div>

    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET" class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label text-sm">Cari</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Kode / nama paket">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-sm">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Aktif"   @selected(request('status')=='Aktif')>Aktif</option>
                        <option value="Selesai" @selected(request('status')=='Selesai')>Selesai</option>
                        <option value="Batal"   @selected(request('status')=='Batal')>Batal</option>
                    </select>
                </div>

                <div class="col-md-5 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    <a href="{{ route('operator.keberangkatan.index') }}"
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

                <table class="table table-compact">

                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Paket</th>
                            <th>Kode</th>
                            <th>Berangkat</th>
                            <th>Pulang</th>
                            <th class="table-center">Kuota</th>
                            <th class="table-center">Status</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $k)
                        <tr>

                            <td data-label="#">
                                {{ $loop->iteration }}
                            </td>

                            <td data-label="Paket">
                                <div class="fw-semibold">
                                    {{ $k->paket->nama_paket ?? '-' }}
                                </div>
                                <div class="text-muted text-sm">
                                    {{ $k->paket->pesawat ?? '' }}
                                </div>
                            </td>

                            <td data-label="Kode">
                                <span class="badge badge-soft-secondary">
                                    {{ $k->kode_keberangkatan }}
                                </span>
                            </td>

                            <td data-label="Berangkat">
                                {{ \Carbon\Carbon::parse($k->tanggal_berangkat)->format('d M Y') }}
                            </td>

                            <td data-label="Pulang">
                                {{ \Carbon\Carbon::parse($k->tanggal_pulang)->format('d M Y') }}
                            </td>

                            <td data-label="Kuota" class="table-center">
                                <span class="badge badge-soft-primary">
                                    {{ $k->seat_terisi }}/{{ $k->kuota }}
                                </span>
                            </td>

                            <td data-label="Status" class="table-center">
                                @php
                                    $statusBadge = [
                                        'Aktif'   => 'badge-soft-success',
                                        'Selesai' => 'badge-soft-primary',
                                        'Batal'   => 'badge-soft-danger',
                                    ][$k->status] ?? 'badge-soft-secondary';
                                @endphp

                                <span class="badge {{ $statusBadge }}">
                                    {{ strtoupper($k->status) }}
                                </span>
                            </td>

                            <td class="table-right col-actions">
                                <div class="table-actions">

                                    <a href="{{ route('operator.keberangkatan.edit', $k->id) }}"
                                       class="btn btn-outline-primary btn-xs"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-outline-danger btn-xs btn-delete"
                                            data-id="{{ $k->id }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $k->id }}"
                                          action="{{ route('operator.keberangkatan.destroy', $k->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-empty">
                                Belum ada data keberangkatan
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
    @if($data->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

{{-- ===============================
   FAB (MOBILE ONLY)
=============================== --}}
<div class="d-md-none">
    <x-fab.add route="{{ route('operator.keberangkatan.create') }}" />
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {

            const id = btn.dataset.id

            Swal.fire({
                title: 'Hapus Keberangkatan?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit()
                }
            })

        })
    })

})
</script>
@endpush
