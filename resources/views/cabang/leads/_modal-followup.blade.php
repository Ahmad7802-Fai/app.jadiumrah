<div class="c-modal" id="followupModal">

  <div class="c-modal__box">

    {{-- HEADER --}}
    <div class="c-modal__header">
      <div>
        <h3 class="c-modal__title">Tambah Follow Up</h3>
        <div class="fs-12 text-muted">
          Catat aktivitas follow up lead
        </div>
      </div>

      <button
        type="button"
        class="c-modal__close"
        data-modal-close>
        ✕
      </button>
    </div>

    {{-- BODY --}}
    <form method="POST"
          action="{{ route('cabang.leads.followup.store', $lead) }}"
          class="c-form">
      @csrf

      <div class="c-modal__body">

        {{-- AKTIVITAS --}}
        <div class="c-form__group">
          <label class="c-form__label">
            Aktivitas <span class="text-danger">*</span>
          </label>

          <select name="aktivitas"
                  class="c-form__input"
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
        <div class="c-form__group">
          <label class="c-form__label">
            Hasil Follow Up <span class="text-danger">*</span>
          </label>

          <textarea name="hasil"
                    class="c-form__textarea"
                    placeholder="Contoh: WA dibalas, tertarik paket Februari"
                    required></textarea>
        </div>

        {{-- NEXT ACTION --}}
        <div class="c-form__group">
          <label class="c-form__label">
            Next Action
            <span class="text-muted">(opsional)</span>
          </label>

          <textarea name="next_action"
                    class="c-form__textarea"
                    placeholder="Contoh: Follow up ulang besok siang"></textarea>

          <div class="fs-11 text-muted mt-2">
            Ditampilkan di timeline follow up
          </div>
        </div>

      </div>

      {{-- FOOTER --}}
      <div class="c-modal__footer">
        <button
          type="button"
          class="c-btn outline"
          data-modal-close>
          Batal
        </button>

        <button type="submit"
                class="c-btn primary">
          💾 Simpan Follow Up
        </button>
      </div>

    </form>

  </div>
</div>
