@extends('layouts.admin')

@section('title', 'Top Up Tabungan Umrah')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header mb-4">
        <div class="page-header-left">
            <a href="{{ url()->previous() }}"
               class="btn btn-icon btn-light d-md-none">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="page-title">Top Up Tabungan Umrah</h1>
                <p class="page-subtitle">
                    Verifikasi setoran tabungan jamaah
                </p>
            </div>
        </div>

        <div class="page-actions">
            <form method="GET">
                <select name="status"
                        class="form-select form-select-sm"
                        onchange="this.form.submit()">
                    @foreach (['PENDING','VALID','REJECTED'] as $s)
                        <option value="{{ $s }}" @selected($status === $s)>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- =====================================================
    | TABLE
    ===================================================== --}}
    <div class="card">
        <div class="table-wrap">
            <table class="table table-compact table-premium mb-0">

                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Jamaah</th>
                        <th>No. Tabungan</th>
                        <th class="table-right">Nominal</th>
                        <th>Bank</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>WA</th>
                        <th class="table-right col-actions">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($topups as $i => $topup)

                    @php
                        $waSent = $topup->status === 'VALID'
                            ? $topup->wa_verified_at
                            : $topup->wa_rejected_at;
                    @endphp

                    <tr>

                        <td data-label="#"> {{ $topups->firstItem() + $i }} </td>

                        {{-- JAMAAH --}}
                        <td data-label="Jamaah">
                            <div class="fw-semibold">
                                {{ $topup->jamaah->nama_lengkap ?? '-' }}
                            </div>
                            <div class="text-muted small">
                                {{ $topup->jamaah->no_hp ?? '-' }}
                            </div>
                        </td>

                        {{-- TABUNGAN --}}
                        <td data-label="No. Tabungan">
                            {{ $topup->tabungan->nomor_tabungan ?? '-' }}
                        </td>

                        {{-- NOMINAL --}}
                        <td data-label="Nominal" class="table-right fw-semibold">
                            Rp {{ number_format($topup->amount, 0, ',', '.') }}
                        </td>

                        {{-- BANK --}}
                        <td data-label="Bank">
                            {{ $topup->bank_sender }}
                            →
                            {{ $topup->bank_receiver }}
                        </td>

                        {{-- TANGGAL --}}
                        <td data-label="Tanggal">
                            {{ $topup->transfer_date
                                ? \Carbon\Carbon::parse($topup->transfer_date)->format('d M Y')
                                : '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td data-label="Status">
                            <span class="table-status">
                                @if($topup->status === 'PENDING')
                                    <span class="badge badge-soft-warning">PENDING</span>
                                @elseif($topup->status === 'VALID')
                                    <span class="badge badge-soft-success">VALID</span>
                                @else
                                    <span class="badge badge-soft-danger">REJECTED</span>
                                @endif
                            </span>
                        </td>

                        {{-- WA --}}
                        <td data-label="WA">
                            <span class="badge {{ $waSent ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                {{ $waSent ? 'Terkirim' : 'Belum' }}
                            </span>
                        </td>

                        {{-- ================= AKSI ================= --}}
                        <td data-label="Aksi" class="table-right">

                            <div class="table-action">

                                {{-- LOCK --}}
                                @if($topup->is_locked)
                                    <span class="badge badge-soft-secondary">
                                        🔒 Ditutup
                                    </span>
                                @endif

                                {{-- FILE --}}
                                @if($topup->proof_file)
                                    <a href="{{ asset('storage/'.$topup->proof_file) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-outline-primary"
                                       title="Bukti Transfer">
                                        <i class="fas fa-file"></i>
                                    </a>
                                @endif

                                @if(
                                    $topup->status === 'VALID' &&
                                    $topup->transaksi &&
                                    $topup->transaksi->buktiSetoran
                                )
                                    <a href="{{ route(
                                        'keuangan.tabungan.bukti-setoran.download',
                                        $topup->transaksi->buktiSetoran->id
                                    ) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-outline-danger"
                                       title="Bukti Setoran">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif

                                {{-- APPROVE / REJECT --}}
                                @if($topup->status === 'PENDING')
                                    <button class="btn btn-success btn-xs"
                                            {{ $topup->is_locked ? 'disabled' : '' }}
                                            onclick="openApproveModal({
                                                id: {{ $topup->id }},
                                                nama: '{{ $topup->jamaah->nama_lengkap ?? '-' }}',
                                                nominal: '{{ number_format($topup->amount,0,',','.') }}',
                                                bank: '{{ $topup->bank_sender }} → {{ $topup->bank_receiver }}'
                                            })"
                                            title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>


                                    <button class="btn btn-xs btn-danger"
                                            {{ $topup->is_locked ? 'disabled' : '' }}
                                            onclick="openRejectModal({{ $topup->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                {{-- RESEND WA --}}
                                @if(in_array($topup->status, ['VALID','REJECTED']))
                                    <button class="btn btn-xs btn-outline-secondary"
                                            {{ $waSent ? 'disabled' : '' }}
                                            onclick="resendWa({{ $topup->id }})"
                                            title="Kirim ulang WA">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                @endif

                            </div>

                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="table-empty">
                            Tidak ada data top up.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="card-footer">
            {{ $topups->links() }}
        </div>
    </div>
</div>

{{-- =====================================================
| MODAL REJECT TOP UP
===================================================== --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="rejectForm">
            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Tolak Top Up Tabungan
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2 text-muted">
                        Masukkan alasan penolakan agar jamaah memahami penyebabnya.
                    </div>

                    <textarea name="admin_note"
                              class="form-control"
                              rows="4"
                              required
                              placeholder="Contoh: Bukti transfer tidak jelas / nominal tidak sesuai"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Tolak Top Up
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
{{-- =====================================================
| MODAL APPROVE TOP UP
===================================================== --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="approveForm">
            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Konfirmasi Approve Top Up
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <div class="text-muted small">Jamaah</div>
                        <div class="fw-semibold" id="approveNama">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Nominal</div>
                        <div class="fw-bold text-success fs-5">
                            Rp <span id="approveNominal">0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small">Transfer Bank</div>
                        <div id="approveBank">-</div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <strong>Perhatian:</strong><br>
                        Saldo tabungan jamaah akan bertambah dan
                        <u>tidak bisa dibatalkan</u>.
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="approveSubmitBtn">
                        <i class="fas fa-check me-1"></i>
                        Approve Top Up
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="appToast"
         class="toast align-items-center text-bg-success border-0"
         role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Berhasil
            </div>
            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
/* =====================================================
| CONFIG
===================================================== */
const INDEX_URL = "{{ route('keuangan.tabungan.topup.index', ['status' => $status]) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

let approveModal;
let rejectModal;

/* =====================================================
| TOAST HELPER
===================================================== */
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('appToast');
    const msgEl   = document.getElementById('toastMessage');

    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    msgEl.innerText   = message;

    new bootstrap.Toast(toastEl, { delay: 2500 }).show();
}

/* =====================================================
| APPROVE MODAL
===================================================== */
function openApproveModal(data) {

    document.getElementById('approveNama').innerText     = data.nama;
    document.getElementById('approveNominal').innerText = data.nominal;
    document.getElementById('approveBank').innerText    = data.bank;

    const form = document.getElementById('approveForm');
    form.action = `/keuangan/tabungan/topup/${data.id}/approve`;

    approveModal = new bootstrap.Modal(
        document.getElementById('approveModal')
    );
    approveModal.show();
}

/* =====================================================
| SUBMIT APPROVE
===================================================== */
document.getElementById('approveForm')
    .addEventListener('submit', function (e) {

        e.preventDefault();

        const btn = document.getElementById('approveSubmitBtn');
        btn.disabled = true;
        btn.innerHTML =
            '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                approveModal.hide();
                showToast('Top up berhasil di-approve');
                setTimeout(() => {
                    window.location.href = INDEX_URL;
                }, 700);
            } else {
                showToast(res.message || 'Gagal approve', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-1"></i> Approve Top Up';
            }
        })
        .catch(() => {
            showToast('Terjadi kesalahan sistem', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1"></i> Approve Top Up';
        });
    });

/* =====================================================
| REJECT MODAL
===================================================== */
function openRejectModal(id) {

    const form = document.getElementById('rejectForm');
    form.action = `/keuangan/tabungan/topup/${id}/reject`;

    rejectModal = new bootstrap.Modal(
        document.getElementById('rejectModal')
    );
    rejectModal.show();
}

/* =====================================================
| SUBMIT REJECT
===================================================== */
document.getElementById('rejectForm')
    .addEventListener('submit', function (e) {

        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML =
            '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                rejectModal.hide();
                showToast('Top up berhasil ditolak', 'warning');
                setTimeout(() => {
                    window.location.href = INDEX_URL;
                }, 700);
            } else {
                showToast(res.message || 'Gagal reject', 'danger');
                btn.disabled = false;
                btn.innerHTML = 'Tolak Top Up';
            }
        })
        .catch(() => {
            showToast('Terjadi kesalahan sistem', 'danger');
            btn.disabled = false;
            btn.innerHTML = 'Tolak Top Up';
        });
    });

/* =====================================================
| RESEND WA
===================================================== */
function resendWa(id) {

    if (!confirm('Kirim ulang WhatsApp ke jamaah?')) return;

    fetch(`/keuangan/tabungan/topup/${id}/resend-wa`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(res => {
        showToast(
            res.message || 'WhatsApp berhasil dikirim',
            'success'
        );
    })
    .catch(() => {
        showToast('Gagal mengirim WhatsApp', 'danger');
    });
}
</script>
@endpush
