@extends('layouts.admin')

@section('title', 'Detail Jamaah')

@section('content')
<div class="container-fluid">

    {{-- ===============================
        PAGE HEADER
    =============================== --}}
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Detail Jamaah</h4>
            <small class="text-muted">
                Review & approval data jamaah
            </small>
        </div>

        <a href="{{ route('operator.jamaah-approval.index') }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ===============================
        FLASH MESSAGE
    =============================== --}}
    @include('components.flash')

    {{-- ===============================
        DETAIL CARD
    =============================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <table class="table table-sm table-borderless mb-0">
                <tbody>
                    <tr>
                        <th width="200">No ID</th>
                        <td class="fw-semibold">{{ $jamaah->no_id }}</td>
                    </tr>

                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $jamaah->nama_lengkap }}</td>
                    </tr>

                    <tr>
                        <th>Nama Passport</th>
                        <td>{{ $jamaah->nama_passport ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Mahram</th>
                        <td>
                            {{ $jamaah->nama_mahram ?? '-' }}
                            <span class="text-muted">
                                ({{ $jamaah->status_mahram ?? '-' }})
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Screening Kesehatan</th>
                        <td class="small">
                            Umroh: <strong>{{ $jamaah->pernah_umroh }}</strong>,
                            Haji: <strong>{{ $jamaah->pernah_haji }}</strong>,
                            Merokok: <strong>{{ $jamaah->merokok }}</strong>,
                            Penyakit: <strong>{{ $jamaah->penyakit_khusus }}</strong>,
                            Kursi Roda: <strong>{{ $jamaah->kursi_roda }}</strong>
                        </td>
                    </tr>

                    <tr>
                        <th>Status Approval</th>
                        <td>
                            @if($jamaah->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($jamaah->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    {{-- ===============================
        ACTION BUTTONS
    =============================== --}}
    @can('approve', $jamaah)
        @if($jamaah->status === 'pending')
            <div class="d-flex gap-2">

                <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#approveModal">
                    <i class="fas fa-check me-1"></i> Approve
                </button>

                <button type="button"
                        class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal">
                    <i class="fas fa-times me-1"></i> Reject
                </button>

            </div>
        @endif
    @endcan

</div>

{{-- =====================================================
    MODAL APPROVE
===================================================== --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST"
              action="{{ route('operator.jamaah-approval.approve', $jamaah->id) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success">
                        Konfirmasi Approve
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-2">
                        Apakah Anda yakin ingin
                        <strong class="text-success">MENYETUJUI</strong>
                        jamaah berikut?
                    </p>

                    <ul class="small mb-0">
                        <li><strong>No ID:</strong> {{ $jamaah->no_id }}</li>
                        <li><strong>Nama:</strong> {{ $jamaah->nama_lengkap }}</li>
                        <li><strong>Paket:</strong> {{ $jamaah->nama_paket }}</li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-success">
                        Ya, Approve
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- =====================================================
    MODAL REJECT
===================================================== --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST"
              action="{{ route('operator.jamaah-approval.reject', $jamaah->id) }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        Tolak Jamaah
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="reason"
                              class="form-control"
                              rows="3"
                              required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Tolak Jamaah
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
