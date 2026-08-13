@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Gallery</h4>

        <div class="d-flex gap-2">
            <form method="GET" class="d-flex" style="width:320px;">
                <input type="text" name="q" class="form-control form-control-sm rounded-pill"
                       placeholder="Cari judul / kategori..."
                       value="{{ request('q') }}">
            </form>

            <a href="{{ route('admin.gallery.create') }}"
               class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Foto
            </a>
        </div>
    </div>

    <!-- Card / Grid -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success rounded-pill">
                    {{ session('success') }}
                </div>
            @endif

            @if($items->isEmpty())
                <div class="text-center py-6 text-muted">
                    <i class="fas fa-image fa-2x mb-2"></i>
                    <div>Belum ada foto galeri.</div>
                </div>
            @else

                <div class="row g-3">
                    @foreach($items as $item)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div style="height:170px; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                                    @if($item->photo && file_exists(public_path('storage/'.$item->photo)))
                                        <img src="{{ asset('storage/'.$item->photo) }}"
                                             alt="{{ $item->title }}"
                                             class="img-fluid w-100"
                                             style="object-fit:cover; height:170px;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-body">
                                    <h6 class="mb-1" style="min-height:44px;">{{ $item->title }}</h6>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $item->category ?? '-' }}</small>
                                        <small class="text-muted">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</small>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('admin.gallery.edit', $item->id) }}"
                                           class="btn btn-sm btn-warning rounded-pill px-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>

                                    <div>
                                        <!-- hidden delete form -->
                                        <form id="delete-form-{{ $item->id }}"
                                              action="{{ route('admin.gallery.destroy', $item->id) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button"
                                                class="btn btn-sm btn-danger rounded-pill px-3 btn-delete"
                                                data-id="{{ $item->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif

            <!-- jika $items adalah LengthAwarePaginator (paginate) tampilkan pagination -->
            @if(method_exists($items, 'links'))
                <div class="mt-4 d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Sedikit polish untuk kartu gallery */
.card .card-body h6 {
    font-weight: 600;
    font-size: 1rem;
}
.card .card-footer .btn {
    box-shadow: none;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Hapus Foto?',
                text: "Foto akan terhapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E02424',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
});
</script>
@endpush
