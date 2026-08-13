@php
    $isEdit = isset($agent);
@endphp

{{-- ===============================
    NAMA
=============================== --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Nama Agent</label>
    <input type="text"
           name="nama"
           class="form-control"
           value="{{ old('nama', $agent->user->nama ?? '') }}"
           required>
</div>

{{-- ===============================
    EMAIL
=============================== --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Email</label>
    <input type="email"
           name="email"
           class="form-control"
           value="{{ old('email', $agent->user->email ?? '') }}"
           required>
</div>

{{-- ===============================
    PHONE
=============================== --}}
<div class="mb-3">
    <label class="form-label">No HP</label>
    <input type="text"
           name="phone"
           class="form-control"
           value="{{ old('phone', $agent->phone ?? '') }}">
</div>

{{-- ===============================
    KOMISI
=============================== --}}
<div class="mb-3">
    <label class="form-label">Komisi (%)</label>
    <input type="number"
           step="0.01"
           min="0"
           max="100"
           name="komisi_persen"
           class="form-control"
           value="{{ old('komisi_persen', $agent->komisi_persen ?? 0) }}">
</div>

{{-- ===============================
    PASSWORD
=============================== --}}
<div class="mb-3">
    <label class="form-label fw-semibold">
        Password
        @if($isEdit)
            <small class="text-muted">(kosongkan jika tidak diubah)</small>
        @endif
    </label>

    <div class="input-group">
        <input type="password"
               name="password"
               id="password"
               class="form-control"
               placeholder="{{ $isEdit ? 'Tidak diubah' : 'Auto generate' }}"
               {{ $isEdit ? '' : 'required' }}>

        <button type="button"
                class="btn btn-outline-secondary"
                onclick="generatePassword()">
            Generate
        </button>
    </div>
</div>


{{-- ===============================
    SCRIPT AUTO PASSWORD
=============================== --}}
@once
@push('scripts')
<script>
function generatePassword() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    let pass = '';
    for (let i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('password').value = pass;
}
</script>
@endpush
@endonce
