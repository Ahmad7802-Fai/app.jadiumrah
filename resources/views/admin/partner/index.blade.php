@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- ===============================
        PAGE HEADER
    =============================== --}}
    <div class="page-header">
        <div>
            <h4 class="page-title">Partner</h4>
            <p class="page-subtitle">Daftar mitra & partner kerja sama</p>
        </div>

        <a href="{{ route('admin.partner.create') }}"
           class="btn btn-primary btn-pill">
            <i class="fas fa-plus me-1"></i> Tambah Partner
        </a>
    </div>


    {{-- ===============================
        CARD
    =============================== --}}
    <div class="card card-clean">

        {{-- CARD HEADER --}}
        <div class="card-header card-header-clean">
            <h5 class="card-title">Daftar Partner</h5>

            <form method="GET" class="filter-inline">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="form-control input-sm input-pill"
                       placeholder="Cari nama partner...">
            </form>
        </div>


        {{-- CARD BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-clean table-hover align-middle">

                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th width="80">Logo</th>
                            <th>Nama</th>
                            <th>Website</th>
                            <th width="120">Dibuat</th>
                            <th width="120" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($partners as $p)
                        <tr>

                            {{-- NO --}}
                            <td>{{ $loop->iteration }}</td>

                            {{-- LOGO --}}
                            <td>
                                <div class="avatar avatar-lg">
                                    <img src="{{ $p->logo
                                        ? asset('storage/'.$p->logo)
                                        : 'https://ui-avatars.com/api/?name='.urlencode($p->nama) }}"
                                         alt="{{ $p->nama }}">
                                </div>
                            </td>

                            {{-- NAMA --}}
                            <td class="fw-semibold">
                                {{ $p->nama }}
                            </td>

                            {{-- WEBSITE --}}
                            <td>
                                @if($p->website)
                                    <a href="{{ $p->website }}"
                                       target="_blank"
                                       class="link-primary">
                                        {{ $p->website }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- CREATED --}}
                            <td class="text-muted text-sm">
                                {{ $p->created_at->format('d M Y') }}
                            </td>

                            {{-- ACTION --}}
                            <td class="text-end">
                                <div class="table-actions">

                                    <a href="{{ route('admin.partner.edit', $p->id) }}"
                                       class="btn btn-warning btn-xs btn-pill"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form id="delete-{{ $p->id }}"
                                          action="{{ route('admin.partner.destroy', $p->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button type="button"
                                            class="btn btn-danger btn-xs btn-pill btn-delete"
                                            data-id="{{ $p->id }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Belum ada data partner
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


{{-- ===============================
    SCRIPTS
=============================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;

        Swal.fire({
            title: 'Hapus Partner?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('delete-' + id).submit();
            }
        });
    });
});
</script>
@endpush
