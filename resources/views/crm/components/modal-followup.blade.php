<div class="modal fade"
     id="followupModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            <form method="POST"
                  action="{{ route('crm.followup.store', $lead) }}"
                  class="form">
                @csrf

                {{-- ================= HEADER ================= --}}
                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Follow Up
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                {{-- ================= BODY ================= --}}
                <div class="modal-body">

                    {{-- AKTIVITAS --}}
                    <div class="form-group">
                        <label>Aktivitas</label>
                        <select name="aktivitas"
                                class="form-select"
                                required>
                            <option value="">Pilih Aktivitas</option>
                            <option value="wa">WhatsApp</option>
                            <option value="telpon">Telepon</option>
                            <option value="dm">DM</option>
                            <option value="meeting">Meeting</option>
                            <option value="presentasi">Presentasi</option>
                            <option value="kunjungan">Kunjungan</option>
                        </select>
                    </div>

                    {{-- HASIL --}}
                    <div class="form-group">
                        <label>Hasil Follow Up</label>
                        <textarea name="hasil"
                                  class="form-textarea"
                                  rows="3"
                                  placeholder="Contoh: WA masuk dari lead online, tertarik paket Februari"
                                  required></textarea>
                    </div>

                    {{-- NEXT ACTION --}}
                    <div class="form-group">
                        <label>Next Action</label>
                        <textarea name="next_action"
                                  class="form-textarea"
                                  rows="2"
                                  placeholder="Contoh: Follow up ulang besok siang"></textarea>

                        <div class="form-hint">
                            Opsional — akan muncul di timeline follow up
                        </div>
                    </div>

                </div>

                {{-- ================= FOOTER ================= --}}
                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Follow Up
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
