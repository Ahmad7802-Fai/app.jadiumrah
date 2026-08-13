@props([
    'lead' => null,
    'sources',
    'submitLabel' => 'Simpan',
    'cancelRoute' => null,
])

<div class="row g-3">

    {{-- ================= NAMA ================= --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Nama</label>
        <input
            type="text"
            name="nama"
            class="form-control"
            required
            value="{{ old('nama', $lead->nama ?? '') }}"
        >
    </div>

    {{-- ================= NO HP ================= --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">No. HP</label>
        <input
            type="text"
            name="no_hp"
            class="form-control"
            required
            value="{{ old('no_hp', $lead->no_hp ?? '') }}"
        >
    </div>

    {{-- ================= EMAIL ================= --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email', $lead->email ?? '') }}"
        >
    </div>

    {{-- ================= SUMBER ================= --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Sumber</label>

        <div class="d-flex gap-2">
            <select
                name="sumber_id"
                class="form-select"
                required
            >
                <option value="">— Pilih Sumber —</option>

                @foreach($sources as $s)
                    <option
                        value="{{ $s->id }}"
                        @selected(old('sumber_id', $lead->sumber_id ?? '') == $s->id)
                    >
                        {{ $s->nama_sumber }}
                    </option>
                @endforeach
            </select>

            {{-- tambah sumber hanya saat create --}}
            @if(!$lead)
                <a
                    href="{{ route('crm.lead-sources.create', [
                        'redirect' => url()->current()
                    ]) }}"
                    class="btn btn-outline-secondary btn-sm"
                >
                    + Tambah
                </a>
            @endif
        </div>
    </div>

    {{-- ================= CHANNEL ================= --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Channel</label>
        <select name="channel" class="form-select">
            <option value="online"
                @selected(old('channel', $lead->channel ?? 'online') === 'online')
            >
                Online
            </option>
            <option value="offline"
                @selected(old('channel', $lead->channel ?? '') === 'offline')
            >
                Offline
            </option>
        </select>
    </div>

    {{-- ================= CATATAN ================= --}}
    <div class="col-md-12">
        <label class="form-label fw-semibold">Catatan</label>
        <textarea
            name="catatan"
            rows="3"
            class="form-control"
            placeholder="Catatan tambahan (opsional)"
        >{{ old('catatan', $lead->catatan ?? '') }}</textarea>
    </div>

</div>

{{-- ================= ACTION ================= --}}
<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel }}
    </button>

    @if($cancelRoute)
        <a href="{{ $cancelRoute }}" class="btn btn-secondary">
            Batal
        </a>
    @endif
</div>
