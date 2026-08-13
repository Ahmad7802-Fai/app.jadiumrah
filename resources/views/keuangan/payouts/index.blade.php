@extends('layouts.admin')

@section('title','Payout Agent')
@section('subtitle','Persetujuan & Pembayaran Komisi Agent')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | FILTER
    ===================================================== --}}
    <div class="card mb-4">

        <div class="card-header">
            <h3 class="card-title">Filter Payout</h3>
        </div>

        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-md-2 col-6">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="requested" @selected(request('status')==='requested')>Requested</option>
                        <option value="approved"  @selected(request('status')==='approved')>Approved</option>
                        <option value="paid"      @selected(request('status')==='paid')>Paid</option>
                        <option value="rejected"  @selected(request('status')==='rejected')>Rejected</option>
                    </select>
                </div>

                <div class="col-md-3 col-6">
                    <select name="agent_id" class="form-control">
                        <option value="">Semua Agent</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" @selected(request('agent_id')==$agent->id)>
                                {{ $agent->user->nama ?? '-' }} ({{ $agent->kode_agent }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-6">
                    <select name="branch_id" class="form-control">
                        <option value="">Semua Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>
                                {{ $branch->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <div class="col-md-2 col-6">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>

                <div class="col-md-2 col-12">
                    <button class="btn btn-primary w-100">
                        🔍 Filter
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- =====================================================
    | TABLE
    ===================================================== --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Daftar Pencairan Komisi Agent</h3>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-compact">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Agent</th>
                            <th>Branch</th>
                            <th>Total Komisi</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="col-actions text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <td data-label="#">#{{ $payout->id }}</td>

                            <td data-label="Agent">
                                <strong>{{ $payout->agent->user->nama ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $payout->agent->kode_agent ?? '-' }}</small>
                            </td>

                            <td data-label="Branch">
                                {{ $payout->branch->nama_cabang ?? '-' }}
                            </td>

                            <td data-label="Total Komisi" class="fw-semibold">
                                Rp {{ number_format($payout->total_komisi,0,',','.') }}
                            </td>

                            <td data-label="Item">
                                {{ $payout->total_item }} item
                            </td>

                            <td data-label="Status">
                                @switch($payout->status)
                                    @case('requested')
                                        <span class="badge badge-info">Requested</span>
                                        @break
                                    @case('approved')
                                        <span class="badge badge-warning">Approved</span>
                                        @break
                                    @case('paid')
                                        <span class="badge badge-success">Paid</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                        @break
                                @endswitch
                            </td>

                            <td data-label="Tanggal" class="text-muted">
                                {{ optional($payout->requested_at)->format('d M Y H:i') }}
                            </td>

                            <td class="col-actions">
                                <div class="table-actions">

                                    <a href="{{ route('keuangan.payout.show',$payout->id) }}"
                                       class="btn btn-outline btn-sm">
                                        👁 Detail
                                    </a>

                                    @if($payout->status === 'requested')
                                        <button class="btn btn-primary btn-sm"
                                                onclick="openApproveModal({{ $payout->id }})">
                                            ✔ Approve
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                                onclick="openRejectModal({{ $payout->id }})">
                                            ✖ Reject
                                        </button>
                                    @endif

                                    @if($payout->status === 'approved')

                                        @php
                                            $agent = $payout->agent;
                                        @endphp

                                        @if(!$agent)
                                            <span class="text-danger small fw-semibold">
                                                ⚠ Agent tidak ditemukan
                                            </span>

                                        @elseif(!$agent->bank_account_number)
                                            <span class="text-danger small fw-semibold">
                                                ⚠ Rekening belum diisi
                                            </span>

                                        @else
                                            <button class="btn btn-success btn-sm"
                                                onclick="openPayModal(this)"
                                                data-id="{{ $payout->id }}"
                                                data-agent="{{ $agent->user->nama ?? '-' }}"
                                                data-kode="{{ $agent->kode_agent ?? '-' }}"
                                                data-bank="{{ $agent->bank_name }}"
                                                data-rekening="{{ $agent->bank_account_number }}"
                                                data-an="{{ $agent->bank_account_name }}"
                                                data-total="Rp {{ number_format($payout->total_komisi,0,',','.') }}">
                                                💸 Pay
                                            </button>
                                        @endif

                                    @endif


                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="table-empty">
                                Belum ada payout.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <div class="card-footer">
            {{ $payouts->links() }}
        </div>

    </div>

</div>

{{-- =====================================================
| MODALS
===================================================== --}}
@include('keuangan.payouts.partials.modals')

{{-- =====================================================
| SCRIPT (NATIVE)
===================================================== --}}
<script>
function openApproveModal(id) {
    document.getElementById('approveForm').action =
        `/keuangan/payout/${id}/approve`;
    toggleModal('approveModal', true);
}
function openRejectModal(id) {
    document.getElementById('rejectForm').action =
        `/keuangan/payout/${id}/reject`;
    toggleModal('rejectModal', true);
}
function openPayModal(btn) {
    document.getElementById('payAgent').innerText    = btn.dataset.agent;
    document.getElementById('payKode').innerText     = btn.dataset.kode;
    document.getElementById('payBank').innerText     = btn.dataset.bank;
    document.getElementById('payRekening').innerText = btn.dataset.rekening;
    document.getElementById('payAn').innerText       = btn.dataset.an;
    document.getElementById('payTotal').innerText    = btn.dataset.total;

    document.getElementById('payForm').action =
        `/keuangan/payout/${btn.dataset.id}/pay`;

    toggleModal('payModal', true);
}
function closeModal(id) {
    toggleModal(id, false);
}
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    modal.classList.toggle('show', show);
    modal.style.display = show ? 'block' : 'none';
    document.body.classList.toggle('modal-open', show);
}
</script>
@endsection

