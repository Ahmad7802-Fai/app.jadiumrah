@extends('layouts.admin')

@section('title', 'Top Up Tabungan Umrah')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Top Up Tabungan Umrah</h4>

    {{-- FILTER STATUS --}}
    <div class="mb-3">
        <a href="?status=PENDING"
           class="btn btn-sm btn-warning {{ $status=='PENDING'?'active':'' }}">
            Pending
        </a>
        <a href="?status=APPROVED"
           class="btn btn-sm btn-success {{ $status=='APPROVED'?'active':'' }}">
            Approved
        </a>
        <a href="?status=REJECTED"
           class="btn btn-sm btn-danger {{ $status=='REJECTED'?'active':'' }}">
            Rejected
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive p-0">

            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                <tr>
                    <th width="30">#</th>
                    <th>Jamaah</th>
                    <th>Nominal</th>
                    <th>Tanggal</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th width="200">Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse($topups as $row)
                    <tr id="row-{{ $row->id }}">
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            <div class="fw-semibold">
                                {{ $row->jamaah->nama_lengkap }}
                            </div>
                            <small class="text-muted">
                                {{ $row->tabungan->nomor_tabungan }}
                            </small>
                        </td>

                        <td>
                            Rp {{ number_format($row->amount,0,',','.') }}
                        </td>

                        <td>
                            {{ $row->transfer_date?->format('d/m/Y') ?? '-' }}
                        </td>

                        {{-- PREVIEW BUKTI --}}
                        <td>
                            @if($row->proof_file)
                                <img src="{{ asset('storage/'.$row->proof_file) }}"
                                     class="img-thumbnail preview-bukti"
                                     data-img="{{ asset('storage/'.$row->proof_file) }}"
                                     style="width:60px;cursor:pointer">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge bg-{{
                                $row->status=='PENDING'?'warning':
                                ($row->status=='APPROVED'?'success':'danger')
                            }}">
                                {{ $row->status }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td>
                            @if($row->status === 'PENDING')

                                <button class="btn btn-sm btn-success btn-approve"
                                        data-id="{{ $row->id }}">
                                    Approve
                                </button>

                                <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal{{ $row->id }}">
                                    Reject
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- MODAL REJECT --}}
                    <div class="modal fade" id="rejectModal{{ $row->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Top Up</h5>
                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <textarea class="form-control admin-note"
                                              placeholder="Catatan admin"
                                              required></textarea>
                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger btn-reject"
                                            data-id="{{ $row->id }}">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="7"
                            class="text-center text-muted py-4">
                            Data tidak tersedia
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-3">
        {{ $topups->links() }}
    </div>
</div>

{{-- MODAL PREVIEW BUKTI --}}
<div class="modal fade" id="modalPreviewBukti" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="previewImage"
                     src=""
                     class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
/* ==========================================
   PREVIEW BUKTI
========================================== */
document.querySelectorAll('.preview-bukti').forEach(img => {
    img.addEventListener('click', function () {
        document.getElementById('previewImage').src = this.dataset.img;
        new bootstrap.Modal(
            document.getElementById('modalPreviewBukti')
        ).show();
    });
});

/* ==========================================
   APPROVE (AJAX)
========================================== */
document.querySelectorAll('.btn-approve').forEach(btn => {
    btn.addEventListener('click', function () {

        if (!confirm('Setujui top up ini?')) return;

        const id = this.dataset.id;
        this.disabled = true;
        this.innerText = 'Memproses...';

        fetch(`/keuangan/tabungan/topup/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                alert(res.message);
                document.getElementById(`row-${id}`).remove();
            } else {
                alert(res.message);
                resetBtn(this);
            }
        })
        .catch(() => {
            alert('Gagal memproses data');
            resetBtn(this);
        });
    });
});

/* ==========================================
   REJECT (AJAX)
========================================== */
document.querySelectorAll('.btn-reject').forEach(btn => {
    btn.addEventListener('click', function () {

        const id = this.dataset.id;
        const modal = this.closest('.modal');
        const note = modal.querySelector('.admin-note').value;

        if (!note) {
            alert('Catatan admin wajib diisi');
            return;
        }

        this.disabled = true;
        this.innerText = 'Memproses...';

        fetch(`/keuangan/tabungan/topup/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ admin_note: note })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                alert(res.message);
                bootstrap.Modal.getInstance(modal).hide();
                document.getElementById(`row-${id}`).remove();
            } else {
                alert(res.message);
                resetBtn(this, 'Reject');
            }
        })
        .catch(() => {
            alert('Gagal memproses data');
            resetBtn(this, 'Reject');
        });
    });
});

function resetBtn(btn, text = 'Approve') {
    btn.disabled = false;
    btn.innerText = text;
}
</script>
@endpush


@push('styles')
<style>
.preview-bukti:hover {
    opacity: .8;
}
.btn[disabled] {
    cursor: not-allowed;
    opacity: .7;
}
</style>
@endpush
