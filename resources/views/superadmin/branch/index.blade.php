@extends('layouts.admin')

@section('title', 'Master Cabang')

@section('content')
<div class="page-branches">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Cabang</h1>
            <p class="text-muted text-sm">
                Kelola data cabang dan distribusi agent
            </p>
        </div>

        @can('create', App\Models\Branch::class)
            <a href="{{ route('superadmin.branch.create') }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Cabang
            </a>
        @endcan
    </div>
{{-- ===============================
   FILTER
=============================== --}}
<form method="GET" class="card card-hover mb-3">
    <div class="card-body">

        <div class="row g-2 align-items-end">

            {{-- KEYWORD --}}
            <div class="col-md-4">
                <label class="form-label text-sm">Cari Cabang</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control"
                       placeholder="Nama / Kode / Kota">
            </div>

            {{-- STATUS --}}
            <div class="col-md-3">
                <label class="form-label text-sm">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Nonaktif</option>
                </select>
            </div>

            {{-- ACTION --}}
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>

                <a href="{{ route('superadmin.branch.index') }}"
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

        @forelse ($branches as $branch)

            <div class="card card-hover card-compact">

                <div class="card-body">

                    {{-- HEADER --}}
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">
                                {{ $branch->nama_cabang }}
                            </div>
                            <div class="text-muted text-sm">
                                {{ $branch->kode_cabang }} • {{ $branch->kota ?? '-' }}
                            </div>
                        </div>

                        <span class="badge {{ $branch->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                            {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>

                    {{-- META --}}
                    <div class="d-flex justify-content-between align-items-center text-sm text-muted mt-2">
                        <span>
                            Agent:
                            <strong>{{ $branch->agents_count }}</strong>
                        </span>

                        @can('toggle', $branch)
                            <div class="form-check">
                                <input type="checkbox"
                                       class="js-toggle-branch"
                                       data-id="{{ $branch->id }}"
                                       {{ $branch->is_active ? 'checked' : '' }}>
                            </div>
                        @endcan
                    </div>

                    {{-- ACTION --}}
                    @can('update', $branch)
                        <div class="mt-2">
                            <a href="{{ route('superadmin.branch.edit', $branch->id) }}"
                               class="btn btn-outline-primary btn-sm btn-block">
                                Edit Cabang
                            </a>
                        </div>
                    @endcan

                </div>
            </div>

        @empty
            <div class="empty-state empty-soft">
                <div class="empty-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="empty-title">Belum ada cabang</div>
                <div class="empty-desc">
                    Tambahkan cabang pertama untuk memulai distribusi agent
                </div>

                @can('create', App\Models\Branch::class)
                    <div class="empty-action">
                        <a href="{{ route('superadmin.branch.create') }}"
                           class="btn btn-primary">
                            Tambah Cabang
                        </a>
                    </div>
                @endcan
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
                                <th width="40">#</th>
                                <th>Kode</th>
                                <th>Nama Cabang</th>
                                <th>Kota</th>
                                <th class="table-center">Agent</th>
                                <th class="table-center">Status</th>
                                <th class="table-center">Aktif</th>
                                <th class="table-right col-actions">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse ($branches as $branch)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}
                                </td>

                                <td class="text-mono">
                                    {{ $branch->kode_cabang }}
                                </td>

                                <td>
                                    <a href="{{ route('superadmin.agent.index', ['branch_id' => $branch->id]) }}"
                                       class="fw-semibold text-decoration-none">
                                        {{ $branch->nama_cabang }}
                                    </a>
                                </td>

                                <td>{{ $branch->kota ?? '-' }}</td>

                                <td class="table-center">
                                    <span class="badge badge-soft-primary">
                                        {{ $branch->agents_count }}
                                    </span>
                                </td>

                                <td class="table-center">
                                    <span class="badge {{ $branch->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                        {{ $branch->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </td>

                                <td class="table-center">
                                    @can('toggle', $branch)
                                        <div class="form-check justify-content-center">
                                            <input type="checkbox"
                                                   class="js-toggle-branch"
                                                   data-id="{{ $branch->id }}"
                                                   {{ $branch->is_active ? 'checked' : '' }}>
                                        </div>
                                    @endcan
                                </td>

                                <td class="table-right">
                                    @can('update', $branch)
                                        <a href="{{ route('superadmin.branch.edit', $branch->id) }}"
                                           class="btn btn-outline-primary btn-xs">
                                            Edit
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="table-empty">
                                    🏢 Data cabang belum tersedia
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
    @if ($branches->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $branches->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.js-toggle-branch').forEach(toggle => {
    toggle.addEventListener('change', function () {
        const checkbox = this

        fetch(`/superadmin/branch/${checkbox.dataset.id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).catch(() => {
            alert('Gagal mengubah status cabang')
            checkbox.checked = !checkbox.checked
        })
    })
})
</script>
@endpush
