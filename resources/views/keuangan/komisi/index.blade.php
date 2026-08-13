@extends('layouts.admin')

@section('title','Approval Komisi Agent')
@section('subtitle','Persetujuan komisi sebelum pencairan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    PAGE HEADER
    ====================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Approval Komisi Agent</h1>
            <p class="text-muted text-sm">
                Komisi pending yang harus divalidasi sebelum bisa diajukan payout
            </p>
        </div>
    </div>


    {{-- =====================================================
    FILTER
    ====================================================== --}}
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET" class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="text-muted text-xs">Agent</label>
                    <select name="agent_id" class="form-control">
                        <option value="">Semua Agent</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}"
                                {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->kode_agent }} — {{ $agent->user->nama ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="text-muted text-xs">Mode</label>
                    <select name="mode" class="form-control">
                        <option value="">Semua</option>
                        <option value="affiliate" {{ request('mode')=='affiliate'?'selected':'' }}>
                            Affiliate
                        </option>
                        <option value="manual" {{ request('mode')=='manual'?'selected':'' }}>
                            Manual
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="text-muted text-xs">Dari</label>
                    <input type="date"
                           name="date_from"
                           value="{{ request('date_from') }}"
                           class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="text-muted text-xs">Sampai</label>
                    <input type="date"
                           name="date_to"
                           value="{{ request('date_to') }}"
                           class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>

            </form>

        </div>
    </div>


    {{-- =====================================================
    TABLE
    ====================================================== --}}
    <div class="card p-0">
        <div class="table-responsive">
            <table class="table table-compact mb-0">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent</th>
                        <th>Jamaah</th>
                        <th>Mode</th>
                        <th class="table-right">%</th>
                        <th class="table-right">Nominal</th>
                        <th>Tanggal</th>
                        <th class="col-actions">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($komisi as $k)
                    <tr>

                        <td>{{ $k->id }}</td>

                        <td>
                            <strong>{{ $k->agent->kode_agent ?? '-' }}</strong><br>
                            <small class="text-muted">
                                {{ $k->agent->phone ?? '-' }}
                            </small>
                        </td>

                        <td>
                            <strong>{{ $k->jamaah->nama_lengkap ?? '-' }}</strong><br>
                            <small class="text-muted">
                                {{ $k->jamaah->no_id ?? '-' }}
                            </small>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark">
                                {{ ucfirst($k->mode) }}
                            </span>
                        </td>

                        <td class="table-right">
                            {{ number_format($k->komisi_persen,2) }}%
                        </td>

                        <td class="table-right fw-semibold">
                            Rp {{ number_format($k->komisi_nominal,0,',','.') }}
                        </td>

                        <td class="text-muted text-sm">
                            {{ $k->created_at?->format('d M Y H:i') }}
                        </td>

                        <td class="col-actions">
                            <div class="table-actions">

                                <a href="{{ route('keuangan.komisi.show', $k->id) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($k->status === 'pending')

                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            onclick="openApproveModal(
                                                {{ $k->id }},
                                                '{{ $k->agent->kode_agent }}',
                                                '{{ $k->jamaah->nama_lengkap }}',
                                                '{{ number_format($k->komisi_nominal,0,',','.') }}'
                                            )">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="openRejectModal({{ $k->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>

                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="table-empty">
                            Tidak ada komisi pending.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

        <div class="card-footer">
            {{ $komisi->links() }}
        </div>
    </div>

</div>


{{-- =====================================================
MODAL APPROVE
===================================================== --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">

            <div class="modal-body text-center">
                <h5 class="fw-bold mb-2">Setujui Komisi?</h5>
                <p class="text-muted text-sm mb-2">
                    Komisi ini akan tersedia untuk payout.
                </p>

                <div class="text-sm">
                    <div id="approveAgent"></div>
                    <div id="approveJamaah"></div>
                    <strong class="d-block mt-1">
                        Rp <span id="approveNominal"></span>
                    </strong>
                </div>
            </div>

            <form method="POST" id="approveForm">
                @csrf
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-success">
                        Ya, Approve
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- =====================================================
MODAL REJECT
===================================================== --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">

            <form method="POST" id="rejectForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tolak Komisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea name="reason"
                              class="form-control"
                              rows="3"
                              placeholder="Alasan penolakan"
                              required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-danger">
                        Tolak
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- =====================================================
SCRIPT
===================================================== --}}
{{-- <script>
function openApproveModal(id, agent, jamaah, nominal) {
    document.getElementById('approveForm').action =
        `/keuangan/komisi/${id}/approve`;

    document.getElementById('approveAgent').innerText  = 'Agent: ' + agent;
    document.getElementById('approveJamaah').innerText = 'Jamaah: ' + jamaah;
    document.getElementById('approveNominal').innerText = nominal;

    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action =
        `/keuangan/komisi/${id}/reject`;

    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

@endsection --}}

{{-- =====================================================
| SCRIPT
===================================================== --}}
<script>
function openApproveModal(id, agent, jamaah, nominal) {
    document.getElementById('approveForm').action =
        `/keuangan/komisi/${id}/approve`;

    document.getElementById('approveAgent').innerText  = 'Agent: ' + agent;
    document.getElementById('approveJamaah').innerText = 'Jamaah: ' + jamaah;
    document.getElementById('approveNominal').innerText = nominal;

    showModal('approveModal');
}

function closeApproveModal() {
    hideModal('approveModal');
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action =
        `/keuangan/komisi/${id}/reject`;

    showModal('rejectModal');
}

function closeRejectModal() {
    hideModal('rejectModal');
}

function showModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
}

function hideModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
}
</script>

@endsection
