@extends('layouts.admin')

@section('title', 'Manajemen Role')

@section('content')
<div class="page-container">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Role</h1>
            <p class="text-muted text-sm">
                Kelola hak akses dan peran pengguna sistem
            </p>
        </div>

        @can('create', App\Models\Role::class)
            <a href="{{ route('superadmin.roles.create') }}"
               class="btn btn-primary btn-sm">
                + Tambah Role
            </a>
        @endcan
    </div>

    {{-- ===============================
       SEARCH
    ================================ --}}
    <div class="card card-soft mb-3">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2 justify-content-end">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control input-sm"
                       placeholder="Cari role...">

                <button class="btn btn-secondary btn-sm">
                    Cari
                </button>
            </form>
        </div>
    </div>

    {{-- ===============================
       MOBILE — CARD LIST
    ================================ --}}
    <div class="d-md-none card-list">

        @forelse($roles as $role)
            <div class="card card-hover">

                <div class="card-body d-flex flex-column gap-2">

                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">
                                {{ $role->role_name }}
                            </div>
                            <div class="text-muted text-sm text-truncate">
                                {{ $role->description ?: '—' }}
                            </div>
                        </div>

                        <span class="badge badge-soft-primary">
                            ROLE
                        </span>
                    </div>

                    <div class="text-muted text-sm">
                        Dibuat {{ $role->created_at?->format('d M Y') ?? '-' }}
                    </div>

                    <div class="btn-group">
                        @can('update', $role)
                            <a href="{{ route('superadmin.roles.edit', $role->id) }}"
                               class="btn btn-outline-primary btn-sm">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $role)
                            <button type="button"
                                    class="btn btn-soft-danger btn-sm btn-delete"
                                    data-id="{{ $role->id }}">
                                Hapus
                            </button>
                        @endcan
                    </div>

                    <form id="delete-form-{{ $role->id }}"
                          action="{{ route('superadmin.roles.destroy', $role->id) }}"
                          method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>
        @empty
            <div class="empty-state empty-soft">
                <div class="empty-icon">🛡️</div>
                <div class="empty-title">Belum ada role</div>
                <div class="empty-desc">
                    Tambahkan role untuk mengatur hak akses sistem
                </div>
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
                                <th>Role</th>
                                <th>Keterangan</th>
                                <th width="140">Dibuat</th>
                                <th class="table-right col-actions">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $role->role_name }}
                                </td>

                                <td class="text-muted">
                                    {{ $role->description ?: '-' }}
                                </td>

                                <td>
                                    {{ $role->created_at?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="table-right">
                                    <div class="table-actions">

                                        @can('update', $role)
                                            <a href="{{ route('superadmin.roles.edit', $role->id) }}"
                                               class="btn btn-outline-primary btn-xs"
                                               title="Edit">
                                                ✏️
                                            </a>
                                        @endcan

                                        @can('delete', $role)
                                            <button type="button"
                                                    class="btn btn-soft-danger btn-xs"
                                                    data-id="{{ $role->id }}"
                                                    title="Hapus">
                                                🗑
                                            </button>
                                        @endcan

                                    </div>

                                    <form id="delete-form-{{ $role->id }}"
                                          action="{{ route('superadmin.roles.destroy', $role->id) }}"
                                          method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-inline">
                                        🛡️ Belum ada role
                                    </div>
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
    @if ($roles->hasPages())
        <div class="mt-4">
            {{ $roles->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {

        const id = this.dataset.id;

        Swal.fire({
            title: 'Hapus Role?',
            text: 'Role yang dihapus dapat memengaruhi user terkait.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(res => {
            if (res.isConfirmed) {
                document.getElementById('delete-form-' + id)?.submit();
            }
        });
    });
});
</script>
@endpush
