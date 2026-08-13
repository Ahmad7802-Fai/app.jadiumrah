@extends('layouts.admin')

@section('title', 'Pipeline Stages')

@section('content')

{{-- ================= CUSTOM BADGE FIX ================= --}}
<style>
    .bg-ju-green {
        background:#0C8C4C !important;
        color:#fff !important;
        font-weight:600;
        padding:6px 12px;
        border-radius:6px;
    }

    .btn-ju-danger {
        background:#dc3545;
        color:#fff;
        border:none;
        width:32px;height:32px;
        display:flex;align-items:center;justify-content:center;
        border-radius:50%;
    }

    .btn-ju-outline {
        border:1.5px solid #0C8C4C;
        color:#0C8C4C;
        width:32px;height:32px;
        display:flex;align-items:center;justify-content:center;
        border-radius:50%;
        background:white;
    }

    .table-premium th,
    .table-premium td{
        padding:12px 14px;
        vertical-align: middle;
        font-size:14px;
    }
</style>


<div class="container-fluid py-3">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-ju-green page-title m-0">Pipeline Stages</h4>

        <button class="btn-ju btn-ju-sm d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="fas fa-plus"></i> Tambah Stage
        </button>
    </div>


    {{-- ================= CARD ================= --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px;">#</th>
                            <th>Nama Tahap</th>
                            <th style="width:100px;">Urutan</th>
                            <th style="width:100px;">Aktif</th>
                            <th class="text-end" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pipelines as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-capitalize">{{ str_replace('_',' ', $p->tahap) }}</td>
                            <td>{{ $p->urutan }}</td>

                            <td>
                                @if ($p->aktif)
                                    <span class="badge bg-ju-green">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-2">

                                    {{-- EDIT --}}
                                    <button class="btn-ju-outline"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $p->id }}">
                                        <i class="fas fa-edit small"></i>
                                    </button>

                                    {{-- DELETE --}}
                                    <form action="{{ route('crm.pipeline.destroy', $p->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus stage ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn-ju-danger">
                                            <i class="fas fa-trash small"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>


    {{-- ================= MODAL ADD ================= --}}
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('crm.pipeline.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Pipeline Stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tahap</label>
                        <input type="text" name="tahap" class="form-control rounded-3" required>
                        <small class="text-muted">Contoh: contacted, meeting, follow_up</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan</label>
                        <input type="number" name="urutan" class="form-control rounded-3" required>
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" name="aktif" class="form-check-input" checked>
                        <label class="form-check-label">Aktifkan</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn-ju btn-ju-sm px-4">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- ================= MODAL EDIT ================= --}}
    @foreach ($pipelines as $p)
    <div class="modal fade" id="modalEdit{{ $p->id }}">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('crm.pipeline.update', $p->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Pipeline Stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tahap</label>
                        <input type="text" name="tahap" value="{{ $p->tahap }}"
                               class="form-control rounded-3" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan</label>
                        <input type="number" name="urutan" value="{{ $p->urutan }}"
                               class="form-control rounded-3" required>
                    </div>

                    <div class="form-check mt-2">
                        <input type="checkbox" name="aktif" class="form-check-input"
                               {{ $p->aktif ? 'checked' : '' }}>
                        <label class="form-check-label">Aktifkan</label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn-ju btn-ju-sm px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endforeach

</div>

@endsection
