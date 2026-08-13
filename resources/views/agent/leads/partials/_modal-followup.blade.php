<div class="modal fade"
     id="followupModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 rounded-top-4">

            <form method="POST"
                  action="{{ route('agent.leads.followup.store', $lead) }}">
                @csrf

                {{-- ================= HEADER ================= --}}
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title fw-semibold mb-0">
                            Tambah Follow Up
                        </h5>
                        <div class="text-muted text-xs">
                            Catat aktivitas follow up lead
                        </div>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                {{-- ================= BODY ================= --}}
                <div class="modal-body pt-2">

                    {{-- AKTIVITAS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Aktivitas
                        </label>

                        <select name="aktivitas"
                                class="form-select"
                                required>
                            <option value="">— Pilih Aktivitas —</option>
                            <option value="wa">WhatsApp</option>
                            <option value="telpon">Telepon</option>
                            <option value="dm">DM</option>
                            <option value="meeting">Meeting</option>
                            <option value="kunjungan">Kunjungan</option>
                            <option value="presentasi">Presentasi</option>
                        </select>
                    </div>

                    {{-- HASIL --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Hasil Follow Up
                        </label>

                        <textarea name="hasil"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Contoh: WA dibalas, tertarik paket Februari"
                                  required></textarea>
                    </div>

                    {{-- NEXT ACTION --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold">
                            Next Action
                            <span class="text-muted fw-normal">(opsional)</span>
                        </label>

                        <textarea name="next_action"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Contoh: Follow up ulang besok siang"></textarea>

                        <div class="form-text">
                            Ditampilkan di timeline follow up
                        </div>
                    </div>

                </div>

                {{-- ================= FOOTER ================= --}}
                <div class="modal-footer border-0 pt-0 gap-2">

                    <button type="button"
                            class="btn btn-gray-soft flex-fill"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-ju flex-fill fw-semibold">
                        💾 Simpan Follow Up
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>
