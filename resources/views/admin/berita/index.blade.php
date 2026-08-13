@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Manajemen Berita</h4>

        <a href="{{ route('admin.berita.create') }}"
           class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Berita
        </a>
    </div>

    <!-- Card Table -->
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between">
                <h5 class="card-title mb-0">Daftar Berita</h5>

                <!-- Search -->
                <form method="GET" class="d-flex gap-2">

                    <select name="kategori" class="form-control form-control-sm rounded-pill" style="width:160px;">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>

                    <input type="text" name="q" class="form-control form-control-sm rounded-pill"
                           placeholder="Cari judul..."
                           value="{{ request('q') }}" style="width: 200px;">

                    <button class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-search"></i>
                    </button>

                </form>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="bg-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Judul</th>
                            <th>Thumbnail</th>
                            <th>Kategori</th>
                            <th>Dibuat</th>
                            <th width="140" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($berita as $item)
                            <tr>

                                <!-- No -->
                                <td>{{ $loop->iteration + ($berita->currentPage()-1)*$berita->perPage() }}</td>

                                <!-- Judul -->
                                <td>
                                    <strong>{{ $item->judul }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item->slug }}</small>
                                </td>

                                <!-- Thumbnail -->
                                <td>
                                    @if($item->thumbnail)
                                        <img src="{{ asset('storage/'.$item->thumbnail) }}"
                                             class="rounded shadow-sm"
                                             style="height:55px; width:90px; object-fit:cover;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <!-- Kategori Badge -->
                                <td>
                                    <span class="badge bg-info px-3 py-2">{{ $item->kategori ?? '-' }}</span>
                                </td>

                                <!-- Created At -->
                                <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>

                                <!-- Actions -->
                                <td class="text-center">
                                    <a href="{{ route('admin.berita.edit', $item->id) }}"
                                       class="btn btn-sm btn-warning rounded-pill px-3">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Hidden form for delete -->
                                    <form id="delete-form-{{ $item->id }}"
                                          action="{{ route('admin.berita.destroy', $item->id) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button type="button"
                                            class="btn btn-sm btn-danger rounded-pill px-3 btn-delete"
                                            data-id="{{ $item->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle"></i>
                                    Belum ada data berita.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end">
                {{ $berita->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>

    </div>

</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".btn-delete").forEach(btn => {

        btn.addEventListener("click", function () {

            let id = this.dataset.id;

            Swal.fire({
                title: "Hapus Berita?",
                text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#E02424",
                cancelButtonColor: "#6B7280",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("delete-form-" + id).submit();
                }
            });

        });

    });

});
</script>
@endpush
