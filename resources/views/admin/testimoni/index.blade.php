@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Testimoni</h4>

        <a href="{{ route('admin.testimoni.create') }}"
           class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus-circle me-1"></i> Tambah Testimoni
        </a>
    </div>

    <div class="row">

        @forelse($items as $item)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 rounded-4">

                    <img src="{{ $item->photo ? asset('storage/' . $item->photo) : 'https://via.placeholder.com/400x250' }}"
                         class="card-img-top rounded-top-4"
                         style="height: 220px; object-fit: cover;">

                    <div class="card-body">

                        <h5 class="fw-bold">{{ $item->nama }}</h5>

                        <p class="text-muted small">{{ $item->pesan }}</p>

                        <p class="mb-2">
                            ⭐ {{ $item->rating }}/5
                        </p>

                        <div class="d-flex gap-2">

                            <a href="{{ route('admin.testimoni.edit', $item->id) }}"
                               class="btn btn-warning btn-sm rounded-pill px-3">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form id="delete-form-{{ $item->id }}"
                                  action="{{ route('admin.testimoni.destroy', $item->id) }}"
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button class="btn btn-danger btn-sm rounded-pill px-3 btn-delete"
                                    data-id="{{ $item->id }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                        </div>

                    </div>
                </div>
            </div>

        @empty
            <p class="text-muted text-center">Belum ada data testimoni.</p>
        @endforelse

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {

        let id = this.dataset.id;

        Swal.fire({
            title: 'Hapus testimoni?',
            text: "Tindakan ini tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E02424',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });

    });
});
</script>
@endpush
