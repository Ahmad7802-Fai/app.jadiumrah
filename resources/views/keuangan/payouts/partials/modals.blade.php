{{-- =====================================================
| MODAL APPROVE
===================================================== --}}
<div class="modal fade modal-sm" id="approveModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">

            <div class="card-body text-center">
                <div class="text-3xl mb-2">✔</div>

                <h4 class="card-title mb-1">
                    Approve Payout?
                </h4>

                <p class="card-subtitle mb-4">
                    Komisi agent akan disetujui dan siap dibayarkan.
                </p>

                <form method="POST" id="approveForm">
                    @csrf

                    <div class="flex justify-center gap-2">
                        <button type="button"
                                class="btn-secondary btn-sm"
                                onclick="closeModal('approveModal')">
                            Batal
                        </button>

                        <button type="submit"
                                class="btn-primary btn-sm">
                            ✔ Approve
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- =====================================================
| MODAL REJECT
===================================================== --}}
<div class="modal fade modal-sm" id="rejectModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">

            <div class="card-header">
                <h4 class="card-title">Tolak Payout</h4>
            </div>

            <form method="POST" id="rejectForm">
                @csrf

                <div class="card-body">
                    <textarea name="reason"
                              class="form-input"
                              rows="3"
                              placeholder="Alasan penolakan (wajib)"
                              required></textarea>
                </div>

                <div class="card-footer">
                    <div class="flex justify-end gap-2">
                        <button type="button"
                                class="btn-secondary btn-sm"
                                onclick="closeModal('rejectModal')">
                            Batal
                        </button>

                        <button type="submit"
                                class="btn-danger btn-sm">
                            ✖ Tolak
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- =====================================================
| MODAL PAY
===================================================== --}}
<div class="modal fade modal-sm" id="payModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">

            <div class="card-body text-sm">

                <div class="text-center mb-3">
                    <div class="text-3xl">💸</div>
                    <h4 class="card-title mt-1">
                        Konfirmasi Pembayaran
                    </h4>
                </div>

                <div class="space-y-1 mb-3">
                    <div><strong>Agent</strong> : <span id="payAgent">-</span></div>
                    <div><strong>Kode</strong> : <span id="payKode">-</span></div>
                </div>

                <hr class="my-2">

                <div class="space-y-1 mb-3">
                    <div><strong>Bank</strong> : <span id="payBank">-</span></div>
                    <div><strong>No Rekening</strong> : <span id="payRekening">-</span></div>
                    <div><strong>Atas Nama</strong> : <span id="payAn">-</span></div>
                </div>

                <hr class="my-2">

                <div class="text-center mb-3">
                    <div class="card-subtitle">Total Dibayarkan</div>
                    <div class="text-lg font-bold text-success" id="payTotal">
                        Rp 0
                    </div>
                </div>

                <p class="text-xs text-muted text-center mb-3">
                    Snapshot rekening akan disimpan untuk audit.
                </p>

                <form method="POST" id="payForm">
                    @csrf

                    <div class="flex justify-center gap-2">
                        <button type="button"
                                class="btn-secondary btn-sm"
                                onclick="closeModal('payModal')">
                            Batal
                        </button>

                        <button type="submit"
                                class="btn-primary btn-sm">
                            💸 Tandai Paid
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
