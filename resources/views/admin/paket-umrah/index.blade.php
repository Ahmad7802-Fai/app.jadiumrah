@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Paket Umrah</h4>

        <a href="{{ route('admin.paket-umrah.create') }}"
           class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Paket
        </a>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">

            <form class="row g-2">

                <!-- SEARCH -->
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control rounded-pill"
                        placeholder="Cari judul / hotel..."
                        value="{{ request('q') }}">
                </div>

                <!-- STATUS -->
                <div class="col-md-3">
                    <select name="status" class="form-control rounded-pill">
                        <option value="">-- Status --</option>
                        <option value="Aktif" {{ request('status')=='Aktif'?'selected':'' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status')=='Tidak Aktif'?'selected':'' }}>Tidak Aktif</option>
                    </select>
                </div>

                <!-- DURASI -->
                <div class="col-md-3">
                    <select name="durasi" class="form-control rounded-pill">
                        <option value="">-- Durasi --</option>
                        @foreach($durasiList as $d)
                            <option value="{{ $d }}" {{ request('durasi')==$d?'selected':'' }}>
                                {{ $d }} Hari
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- BUTTON -->
                <div class="col-md-2">
                    <button class="btn btn-dark rounded-pill w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- LIST DATA -->
    <div class="row">

        @forelse ($data as $p)
        <div class="col-md-4 mb-4">

            <div class="card shadow-sm border-0 rounded-4 premium-hover">

                <!-- THUMBNAIL -->
                <img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('noimage.jpg') }}"
                     class="card-img-top rounded-top-4"
                     style="height: 220px; object-fit: cover;">

                <div class="card-body">

                    <!-- JUDUL -->
                    <h5 class="fw-bold mb-1">{{ $p->title }}</h5>

                    <!-- BADGE DURASI + STATUS -->
                    <div class="mb-2">

                        <span class="badge bg-info px-2 py-1">
                            {{ $p->durasi }} Hari
                        </span>

                        <span class="badge bg-warning text-dark px-2 py-1">
                            Seat {{ $p->seat }}
                        </span>

                        @if($p->status == 'Aktif')
                            <span class="badge bg-success px-2 py-1">Aktif</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1">Tidak Aktif</span>
                        @endif

                    </div>

                    <!-- DETAIL SINGKAT -->
                    <div class="text-muted small mb-2">
                        <i class="fas fa-plane-departure"></i>
                        Berangkat: {{ date('d M Y', strtotime($p->tglberangkat)) }}
                    </div>

                    <div class="text-muted small mb-1">
                        <i class="fas fa-hotel"></i> Mekkah: 
                        {{ $p->hotmekkah }} ⭐{{ $p->rathotmekkah }}
                    </div>

                    <div class="text-muted small mb-3">
                        <i class="fas fa-hotel"></i> Madinah: 
                        {{ $p->hotmadinah }} ⭐{{ $p->rathotmadinah }}
                    </div>

                    <!-- FASILITAS -->
                    <div class="small mb-3">

                        @if($p->thaif == 'Ya')
                            <span class="badge bg-primary">Thaif</span>
                        @endif

                        @if($p->dubai == 'Ya')
                            <span class="badge bg-primary">Dubai</span>
                        @endif

                        @if($p->kereta == 'Ya')
                            <span class="badge bg-primary">Kereta Cepat</span>
                        @endif

                    </div>

                    <hr>

                    <!-- ACTION BUTTONS -->
                    <div class="d-flex justify-content-between">

                        <a href="{{ route('admin.paket-umrah.edit', $p->id) }}"
                           class="btn btn-sm btn-warning rounded-pill px-3">
                            <i class="fas fa-edit"></i>
                        </a>

                        <button type="button"
                                class="btn btn-sm btn-danger rounded-pill px-3 btn-delete"
                                data-id="{{ $p->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                        <form id="delete-form-{{ $p->id }}"
                              action="{{ route('admin.paket-umrah.destroy', $p->id) }}"
                              method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>

                    </div>

                </div>
            </div>

        </div>
        @empty

            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-info-circle"></i> Belum ada paket umrah.
            </div>

        @endforelse

    </div>

    <div class="d-flex justify-content-center">
        {{ $data->appends(request()->all())->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection

@push('styles')
<style>
.premium-hover { transition: .2s; }
.premium-hover:hover { transform: translateY(-4px); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;

        Swal.fire({
            title: 'Hapus Paket?',
            text: "Data paket tidak dapat dipulihkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E02424',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-'+id).submit();
            }
        });
    });
});
</script>
@endpush
