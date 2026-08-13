@extends('layouts.admin')

@section('content')
<div class="page-wrapper">

    {{-- ===============================
        PAGE HEADER
    ================================ --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Team</h1>
            <p class="page-subtitle">Kelola anggota team dan struktur internal</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('admin.team.create') }}"
               class="btn btn-primary btn-pill">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Anggota</span>
            </a>
        </div>
    </div>


    {{-- ===============================
        CARD
    ================================ --}}
    <div class="card card-soft">

        {{-- CARD HEADER --}}
        <div class="card-header card-header-soft">
            <div class="card-header-left">
                <h3 class="card-title">Daftar Team</h3>
            </div>

            <div class="card-header-right">
                <form method="GET" class="form-search">
                    <input type="text"
                           name="q"
                           class="form-input form-input-sm"
                           placeholder="Cari nama..."
                           value="{{ request('q') }}">
                </form>
            </div>
        </div>

        {{-- CARD BODY --}}
        <div class="card-body">

            <div class="table-wrapper">
                <table class="table table-soft">

                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th width="80">Foto</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Dibuat</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($team as $t)
                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                {{-- FOTO --}}
                                <td>
                                    <div class="avatar">
                                        <img
                                            src="{{ $t->photo
                                                ? asset('storage/'.$t->photo)
                                                : 'https://ui-avatars.com/api/?size=128&background=0D8ABC&color=fff&name=' . urlencode($t->nama)
                                            }}"
                                            alt="{{ $t->nama }}">
                                    </div>
                                </td>

                                {{-- NAMA --}}
                                <td class="fw-semibold text-dark">
                                    {{ $t->nama }}
                                </td>

                                {{-- JABATAN --}}
                                <td class="text-muted">
                                    {{ $t->jabatan }}
                                </td>

                                {{-- CREATED --}}
                                <td>
                                    {{ $t->created_at?->format('d M Y') ?? '-' }}
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center">
                                    <div class="table-actions">

                                        <a href="{{ route('admin.team.edit', $t->id) }}"
                                           class="btn btn-warning btn-sm btn-pill">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form id="delete-form-{{ $t->id }}"
                                              method="POST"
                                              action="{{ route('admin.team.destroy', $t->id) }}"
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-pill btn-delete"
                                                data-id="{{ $t->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty">
                                    <i class="fas fa-users"></i>
                                    <span>Belum ada anggota team</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            Swal.fire({
                title: 'Hapus Anggota?',
                text: 'Data yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e02424',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endpush
