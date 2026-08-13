@extends('layouts.admin')

@section('title','Master Paket')

@section('content')
<div class="page-master-paket">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Paket</h1>
            <p class="page-subtitle">
                Kelola paket umroh (hotel, harga, maskapai)
            </p>
        </div>

        {{-- DESKTOP ACTION --}}
        <div class="page-actions">
            <a href="{{ route('operator.master-paket.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Paket
            </a>
        </div>
    </div>

    {{-- ===============================
       FILTER
    ================================ --}}
    <form method="GET" class="card card-hover mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">

                {{-- SEARCH --}}
                <div class="col-md-4">
                    <label class="form-label text-sm">Cari Paket</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           class="form-control"
                           placeholder="Nama paket, hotel, pesawat">
                </div>

                {{-- STATUS --}}
                <div class="col-md-3">
                    <label class="form-label text-sm">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="Aktif" @selected(request('status')=='Aktif')>
                            Aktif
                        </option>
                        <option value="Tidak Aktif" @selected(request('status')=='Tidak Aktif')>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

                {{-- ACTION --}}
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    @if(request()->hasAny(['q','status']))
                        <a href="{{ route('operator.master-paket.index') }}"
                           class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    @endif
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
                            <th width="40">#</th>
                            <th>Paket</th>
                            <th>Hotel</th>
                            <th>Harga</th>
                            <th class="table-center">Status</th>
                            <th>Dibuat</th>
                            <th class="table-right col-actions">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($data as $p)
                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            {{-- PAKET --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $p->nama_paket }}
                                </div>
                                <div class="text-muted text-sm">
                                    <i class="fas fa-plane"></i>
                                    {{ $p->pesawat ?? '-' }}
                                </div>
                            </td>

                            {{-- HOTEL --}}
                            <td class="text-sm">
                                <div>
                                    <span class="text-muted">Mekkah:</span>
                                    <strong>{{ $p->hotel_mekkah }}</strong>
                                </div>
                                <div>
                                    <span class="text-muted">Madinah:</span>
                                    <strong>{{ $p->hotel_madinah }}</strong>
                                </div>
                            </td>

                            {{-- HARGA --}}
                            <td class="text-sm">
                                <div>
                                    <span class="badge badge-soft-primary">
                                        Quad: Rp {{ number_format($p->harga_quad) }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span class="badge badge-soft-warning">
                                        Triple: Rp {{ number_format($p->harga_triple) }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span class="badge badge-soft-success">
                                        Double: Rp {{ number_format($p->harga_double) }}
                                    </span>
                                </div>
                            </td>

                            {{-- STATUS --}}
                            <td class="table-center">
                                <span class="badge {{ $p->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                    {{ $p->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                                </span>
                            </td>

                            {{-- CREATED --}}
                            <td class="text-sm">
                                {{ $p->created_at->format('d M Y') }}
                            </td>

                            {{-- ACTION --}}
                            <td class="table-right col-actions">
                                <div class="table-actions">

                                    <a href="{{ route('operator.master-paket.edit', $p->id) }}"
                                       class="btn btn-outline-primary btn-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-outline-danger btn-xs js-delete"
                                            data-id="{{ $p->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $p->id }}"
                                          action="{{ route('operator.master-paket.destroy', $p->id) }}"
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
                            <td colspan="7" class="table-empty">
                                Belum ada data paket
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
   FAB — MOBILE ONLY
=============================== --}}
<div class="d-md-none">
    <x-fab.add route="{{ route('operator.master-paket.create') }}" />
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.js-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            Swal.fire({
                title: 'Hapus Master Paket?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });

});
</script>
@endpush
