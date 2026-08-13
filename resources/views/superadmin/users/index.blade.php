@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="page-users">

    {{-- ===============================
       PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen User</h1>
            <p class="text-muted text-sm">
                Kelola akun dan role pengguna sistem
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            {{-- SEARCH (DESKTOP ONLY) --}}
            <form method="GET" class="d-none d-md-block">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control input-sm"
                       placeholder="Cari nama / email">
            </form>

            <a href="{{ route('superadmin.users.create') }}"
               class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah User
            </a>
        </div>
    </div>

    {{-- ===============================
       MOBILE MODE — CARD LIST
    ================================ --}}
    <div class="d-md-none card-list">

        @forelse($users as $u)

            @php
                $roleBadge = match ($u->role) {
                    'SUPERADMIN' => 'badge-soft-danger',
                    'ADMIN'      => 'badge-soft-primary',
                    'KEUANGAN'   => 'badge-soft-warning',
                    'OPERATOR'   => 'badge-soft-info',
                    'SALES'      => 'badge-soft-success',
                    default      => 'badge-soft-secondary',
                };
            @endphp

            <div class="card card-hover">
                <div class="card-body card-compact">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between gap-2">
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">
                                {{ $u->nama }}
                            </div>
                            <div class="text-muted text-sm text-truncate">
                                {{ $u->email }}
                            </div>
                        </div>

                        <span class="badge {{ $roleBadge }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </div>

                    {{-- META --}}
                    <div class="text-muted text-sm mt-1">
                        Dibuat {{ $u->created_at->format('d M Y') }}
                    </div>

                    {{-- ACTION --}}
                    <div class="btn-group mt-2">
                        <a href="{{ route('superadmin.users.edit', $u->id) }}"
                           class="btn btn-outline-primary btn-sm btn-block">
                            Edit
                        </a>

                        <button type="button"
                                class="btn btn-outline-danger btn-sm btn-block btn-delete"
                                data-id="{{ $u->id }}">
                            Hapus
                        </button>
                    </div>

                    <form id="delete-form-{{ $u->id }}"
                          method="POST"
                          action="{{ route('superadmin.users.destroy', $u->id) }}">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>

        @empty
            <div class="empty-state empty-soft">
                <div class="empty-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="empty-title">Belum ada user</div>
                <div class="empty-desc">
                    Tambahkan user pertama untuk mulai mengelola sistem
                </div>
                <div class="empty-action">
                    <a href="{{ route('superadmin.users.create') }}"
                       class="btn btn-primary">
                        Tambah User
                    </a>
                </div>
            </div>
        @endforelse

    </div>

    {{-- ===============================
       DESKTOP MODE — TABLE
    ================================ --}}
    <div class="card">
        <div class="card card-hover">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-compact">

                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th class="col-actions table-right">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($users as $u)

                            @php
                                $roleBadge = match ($u->role) {
                                    'SUPERADMIN' => 'badge-soft-danger',
                                    'ADMIN'      => 'badge-soft-primary',
                                    'KEUANGAN'   => 'badge-soft-warning',
                                    'OPERATOR'   => 'badge-soft-info',
                                    'SALES'      => 'badge-soft-success',
                                    default      => 'badge-soft-secondary',
                                };
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $u->nama }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge {{ $roleBadge }}">
                                        {{ strtoupper($u->role) }}
                                    </span>
                                </td>
                                <td>{{ $u->created_at->format('d M Y') }}</td>
                                <td class="col-actions">
                                    <div class="table-actions">
                                        <a href="{{ route('superadmin.users.edit', $u->id) }}"
                                           class="btn btn-outline-primary"
                                           title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                data-id="{{ $u->id }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $u->id }}"
                                          method="POST"
                                          action="{{ route('superadmin.users.destroy', $u->id) }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-inline">
                                        Belum ada user
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
    @if ($users->hasPages())
        <div class="pagination pagination-simple">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    if (confirm('Yakin ingin menghapus user ini?')) {
        document.getElementById('delete-form-' + id)?.submit();
    }
});
</script>
@endpush
