@if($pnr->status === 'ON_FLOW')
<div class="ju-modal" id="confirmPnrModal">

    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content modal-confirm is-danger">

            <div class="modal-header">
                <div class="modal-title">
                    Confirm PNR
                </div>

                <button type="button"
                        class="btn-close"
                        data-modal-close>
                    ×
                </button>
            </div>

            <div class="modal-body">
                <div class="confirm-icon">⚠️</div>

                <p class="mb-sm">
                    Anda yakin ingin <strong>mengonfirmasi PNR</strong> ini?
                </p>

                <ul class="text-muted text-sm mb-md">
                    <li>PNR Code: <strong>{{ $pnr->pnr_code }}</strong></li>
                    <li>Client: {{ $pnr->client->nama ?? '-' }}</li>
                    <li>Jumlah sector: {{ $pnr->routes->count() }}</li>
                </ul>

                <p class="text-danger text-sm">
                    Setelah confirm, data PNR tidak bisa diubah.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-light btn-sm"
                        data-modal-close>
                    Cancel
                </button>

                <form method="POST"
                      action="{{ route('ticketing.pnr.confirm', $pnr) }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        Yes, Confirm
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endif
