@extends('layouts.admin')

@section('title','Refund Approval')

@section('content')

<div class="page page--narrow">

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header mb-md">
    <div>
        <div class="page-title">Refund Approval</div>
        <div class="text-sm text-muted">
            Daftar refund yang menunggu persetujuan
        </div>
    </div>
</div>

{{-- ======================================================
| TABLE
====================================================== --}}
<div class="card">
    <div class="card-body p-0">

        <div class="table-wrap">
            <table class="table table-compact">

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>PNR</th>
                        <th>Invoice</th>
                        <th class="table-right">Amount</th>
                        <th>Status</th>
                        <th class="col-actions table-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($refunds as $refund)
                    <tr>

                        {{-- DATE --}}
                        <td>
                            {{ optional($refund->refunded_at)?->format('d M Y') ?? '-' }}
                        </td>

                        {{-- PNR --}}
                        <td class="mono">
                            {{ optional($refund->invoice?->pnr)->pnr_code ?? '-' }}
                        </td>

                        {{-- INVOICE --}}
                        <td class="mono text-muted">
                            {{ $refund->invoice->invoice_number ?? '-' }}
                        </td>

                        {{-- AMOUNT --}}
                        <td class="table-right fw-semibold">
                            @money($refund->amount)
                        </td>

                        {{-- STATUS --}}
                        <td>
                            @include('ticketing.refund._status_badge', [
                                'status' => $refund->approval_status
                            ])
                        </td>

                        {{-- ACTION --}}
                        <td class="table-right col-actions">
                            @if($refund->approval_status === 'PENDING')
                                <div class="table-action">

                                    <button
                                        class="btn btn-primary btn-xs"
                                        data-open-modal
                                        data-type="approve"
                                        data-title="Approve Refund"
                                        data-message="Yakin approve refund sebesar @money($refund->amount)?"
                                        data-action="{{ route('ticketing.refund.approve', $refund) }}"
                                        data-submit="Approve">
                                        Approve
                                    </button>

                                    <button
                                        class="btn btn-danger btn-xs"
                                        data-open-modal
                                        data-type="reject"
                                        data-title="Reject Refund"
                                        data-message="Yakin reject refund ini?"
                                        data-action="{{ route('ticketing.refund.reject', $refund) }}"
                                        data-submit="Reject">
                                        Reject
                                    </button>

                                </div>
                            @else
                                <span class="text-muted text-xs italic">
                                    Final
                                </span>
                            @endif
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            Tidak ada refund menunggu approval
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- ======================================================
| CONFIRM MODAL (REUSABLE)
====================================================== --}}
<div class="ju-modal" id="confirmModal">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content modal-confirm" id="modalContent">

            <div class="modal-header">
                <div class="modal-title" id="modalTitle">Confirm</div>
                <button type="button" class="btn-close" data-close-modal>×</button>
            </div>

            <div class="modal-body">
                <div class="confirm-icon" id="modalIcon">⚠️</div>
                <p id="modalMessage" class="text-sm"></p>
            </div>

            <div class="modal-footer">
                <form method="POST" id="modalForm">
                    @csrf

                    <button type="button"
                            class="btn btn-outline btn-sm"
                            data-close-modal>
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary btn-sm"
                            id="modalSubmit">
                        Confirm
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (e) {

    /* ===============================
     | OPEN MODAL
     =============================== */
    const btn = e.target.closest('[data-open-modal]');
    if (btn) {
        const modal   = document.getElementById('confirmModal');
        const content = document.getElementById('modalContent');

        // reset state
        content.classList.remove('is-success', 'is-danger');

        // fill content
        document.getElementById('modalTitle').innerText   = btn.dataset.title;
        document.getElementById('modalMessage').innerText = btn.dataset.message;
        document.getElementById('modalForm').action       = btn.dataset.action;
        document.getElementById('modalSubmit').innerText  = btn.dataset.submit;

        // variant
        if (btn.dataset.type === 'approve') {
            content.classList.add('is-success');
            document.getElementById('modalIcon').innerText = '✅';
            document.getElementById('modalSubmit').className =
                'btn btn-success btn-sm';
        } else {
            content.classList.add('is-danger');
            document.getElementById('modalIcon').innerText = '⚠️';
            document.getElementById('modalSubmit').className =
                'btn btn-danger btn-sm';
        }

        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    /* ===============================
     | CLOSE MODAL
     =============================== */
    if (
        e.target.matches('[data-close-modal]') ||
        e.target.closest('[data-close-modal]') ||
        e.target.classList.contains('ju-modal')
    ) {
        const modal = document.getElementById('confirmModal');
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});
</script>
@endpush
