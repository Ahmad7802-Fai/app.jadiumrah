<h3 class="text-lg font-semibold mb-2">
    Follow Up
</h3>

<form method="POST"
      action="{{ route('agent.leads.followup.store', $lead) }}">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

        {{-- AKTIVITAS --}}
        <div>
            <label class="form-label">Aktivitas</label>
            <select name="aktivitas" class="form-select" required>
                <option value="">-- Pilih Aktivitas --</option>
                <option value="wa">WhatsApp</option>
                <option value="telpon">Telepon</option>
                <option value="kunjungan">Kunjungan</option>
                <option value="presentasi">Presentasi</option>
                <option value="dm">DM</option>
                <option value="meeting">Meeting</option>
            </select>
        </div>

        {{-- FOLLOW UP DATE --}}
        <div>
            <label class="form-label">Tanggal Follow Up</label>
            <input type="date"
                   name="followup_date"
                   class="form-control">
        </div>

        {{-- HASIL --}}
        <div class="md:col-span-2">
            <label class="form-label">Hasil Follow Up</label>
            <textarea name="hasil"
                      rows="3"
                      class="form-control"
                      required
                      placeholder="Ringkasan hasil follow up"></textarea>
        </div>

        {{-- NEXT ACTION --}}
        <div class="md:col-span-2">
            <label class="form-label">Next Action</label>
            <textarea name="next_action"
                      rows="2"
                      class="form-control"
                      placeholder="Tindakan selanjutnya (opsional)"></textarea>
        </div>

    </div>

    <button class="btn-primary btn-sm mt-3">
        Simpan Follow Up
    </button>
</form>
{{-- RIWAYAT --}}
<div class="mt-4 space-y-2">
    @foreach($lead->activities as $act)
        <div class="text-sm border p-2 rounded">
            <strong>{{ strtoupper($act->aktivitas) }}</strong>
            <div>{{ $act->hasil }}</div>

            @if($act->next_action)
                <div class="text-xs text-gray-600">
                    Next: {{ $act->next_action }}
                </div>
            @endif

            <small class="text-gray-500">
                {{ $act->created_at->format('d M Y H:i') }}
            </small>
        </div>
    @endforeach
</div>
