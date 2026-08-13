@php
    use Illuminate\Support\Carbon;

    $isEdit = isset($jamaah) && $jamaah;

    $hasPayment = $isEdit && method_exists($jamaah,'payments')
        && $jamaah->payments()
            ->where('status','valid')
            ->where('is_deleted',0)
            ->exists();
@endphp

{{-- ================= FLAGS FOR JS ================= --}}
<input type="hidden" id="is-edit" value="{{ $isEdit ? 1 : 0 }}">

{{-- ================= BACK ================= --}}
<div class="mb-3">
    <a href="{{ route('operator.daftar-jamaah.index') }}"
       class="btn btn-outline-success rounded-pill">
        ← Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
<div class="card-body">

{{-- =====================================================
KEBERANGKATAN & PAKET
===================================================== --}}
<h6 class="fw-bold mb-3">Keberangkatan & Paket</h6>
<div class="row g-3 mb-4">

    <div class="col-md-6">
        <label class="fw-semibold">Keberangkatan *</label>
        <select id="keberangkatan"
                name="id_keberangkatan"
                class="form-control rounded-pill"
                {{ $hasPayment ? 'disabled' : '' }}
                required>
            <option value="">-- Pilih --</option>
            @foreach($keberangkatan as $k)
                <option value="{{ $k->id }}"
                    {{ old('id_keberangkatan', $jamaah->id_keberangkatan ?? '') == $k->id ? 'selected' : '' }}>
                    {{ $k->kode_keberangkatan }} —
                    {{ Carbon::parse($k->tanggal_berangkat)->format('d M Y') }}
                </option>
            @endforeach
        </select>

        {{-- fallback kalau disabled --}}
        @if($hasPayment)
            <input type="hidden" name="id_keberangkatan"
                   value="{{ $jamaah->id_keberangkatan }}">
        @endif
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Paket</label>
        <input id="nama-paket"
               class="form-control rounded-pill bg-light"
               readonly>

        <input type="hidden"
               id="id_paket_master"
               name="id_paket_master"
               value="{{ old('id_paket_master', $jamaah->id_paket_master ?? '') }}">
    </div>

</div>

{{-- =====================================================
IDENTITAS JAMAAH
===================================================== --}}
<h6 class="fw-bold mb-3">Identitas Jamaah</h6>
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <label>No ID</label>
        <input class="form-control rounded-pill bg-light fw-bold"
               value="{{ old('no_id', $jamaah->no_id ?? $autoNoID ?? '') }}"
               readonly>
        <input type="hidden" name="no_id"
               value="{{ old('no_id', $jamaah->no_id ?? $autoNoID ?? '') }}">
    </div>

    <div class="col-md-4">
        <label>Nama Lengkap *</label>
        <input name="nama_lengkap"
               class="form-control rounded-pill"
               value="{{ old('nama_lengkap', $jamaah->nama_lengkap ?? '') }}"
               required>
    </div>

    <div class="col-md-4">
        <label>Nama Passport</label>
        <input name="nama_passport"
               class="form-control rounded-pill"
               value="{{ old('nama_passport', $jamaah->nama_passport ?? '') }}">
    </div>

    <div class="col-md-4">
        <label>Nama Ayah *</label>
        <input name="nama_ayah"
               class="form-control rounded-pill"
               value="{{ old('nama_ayah', $jamaah->nama_ayah ?? '') }}"
               required>
    </div>

    <div class="col-md-4">
        <label>NIK *</label>
        <input name="nik"
               class="form-control rounded-pill"
               value="{{ old('nik', $jamaah->nik ?? '') }}"
               required>
    </div>

    <div class="col-md-4">
        <label>No HP *</label>
        <input name="no_hp"
               class="form-control rounded-pill"
               value="{{ old('no_hp', $jamaah->no_hp ?? '') }}"
               required>
    </div>

</div>

{{-- =====================================================
KELAHIRAN & STATUS
===================================================== --}}
<h6 class="fw-bold mb-3">Kelahiran & Status</h6>
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <label>Tempat Lahir *</label>
        <input name="tempat_lahir"
               class="form-control rounded-pill"
               value="{{ old('tempat_lahir', $jamaah->tempat_lahir ?? '') }}"
               required>
    </div>

    <div class="col-md-4">
        <label>Tanggal Lahir *</label>
        <input type="date"
               id="tgl-lahir"
               name="tanggal_lahir"
               class="form-control rounded-pill"
               value="{{ old(
                    'tanggal_lahir',
                    $isEdit && $jamaah->tanggal_lahir
                        ? Carbon::parse($jamaah->tanggal_lahir)->format('Y-m-d')
                        : ''
               ) }}"
               required>
    </div>

    <div class="col-md-4">
        <label>Usia</label>
        <input id="usia"
               class="form-control rounded-pill bg-light"
               readonly>
    </div>

    <div class="col-md-4">
        <label>Jenis Kelamin *</label>
        <select name="jenis_kelamin"
                class="form-control rounded-pill"
                required>
            <option value="L" {{ old('jenis_kelamin', $jamaah->jenis_kelamin ?? '')=='L'?'selected':'' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin', $jamaah->jenis_kelamin ?? '')=='P'?'selected':'' }}>Perempuan</option>
        </select>
    </div>

    <div class="col-md-4">
        <label>Status Pernikahan *</label>
        <select name="status_pernikahan"
                class="form-control rounded-pill"
                required>
            @foreach(['Belum Menikah','Menikah','Cerai'] as $s)
                <option value="{{ $s }}"
                    {{ old('status_pernikahan', $jamaah->status_pernikahan ?? '')==$s?'selected':'' }}>
                    {{ $s }}
                </option>
            @endforeach
        </select>
    </div>

</div>

{{-- =====================================================
MAHRAM
===================================================== --}}
<h6 class="fw-bold mb-3">Mahram</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label>Nama Mahram</label>
        <input name="nama_mahram"
               class="form-control rounded-pill"
               value="{{ old('nama_mahram', $jamaah->nama_mahram ?? '') }}">
    </div>

    <div class="col-md-6">
        <label>Status Mahram</label>
        <input name="status_mahram"
               class="form-control rounded-pill"
               value="{{ old('status_mahram', $jamaah->status_mahram ?? '') }}">
    </div>
</div>

{{-- =====================================================
HARGA & KAMAR
===================================================== --}}
<h6 class="fw-bold mb-3">Harga & Kamar</h6>
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <label>Tipe Kamar *</label>

        {{-- SELECT TANPA NAME (UI ONLY) --}}
        <select id="tipe-kamar"
                class="form-control rounded-pill"
                {{ $hasPayment ? 'disabled' : '' }}
                required>
            @foreach(['quad','triple','double'] as $t)
                <option value="{{ $t }}"
                    {{ old('tipe_kamar', $jamaah->tipe_kamar ?? 'quad')==$t?'selected':'' }}>
                    {{ ucfirst($t) }}
                </option>
            @endforeach
        </select>

        {{-- 🔥 SINGLE SOURCE OF TRUTH --}}
        <input type="hidden"
               id="tipe-kamar-hidden"
               name="tipe_kamar"
               value="{{ old('tipe_kamar', $jamaah->tipe_kamar ?? 'quad') }}">
    </div>

    <div class="col-md-4">
        <label>Harga Paket</label>

        <input id="harga-view"
               class="form-control rounded-pill bg-light"
               readonly>

        <input type="hidden"
               id="harga_default"
               name="harga_default"
               value="{{ old('harga_default', $jamaah->harga_default ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label>Diskon</label>
        <input type="number"
               name="diskon"
               class="form-control rounded-pill"
               value="{{ old('diskon', $jamaah->diskon ?? 0) }}"
               {{ $hasPayment ? 'disabled' : '' }}>
    </div>

</div>
{{-- =====================================================
TIPE JAMAAH
===================================================== --}}
<h6 class="fw-bold mb-3">Tipe Jamaah</h6>
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <label class="fw-semibold">Tipe Jamaah *</label>

        <select name="tipe_jamaah"
                class="form-control rounded-pill"
                required>

            <option value="reguler"
                {{ old('tipe_jamaah', $jamaah->tipe_jamaah ?? 'reguler') == 'reguler' ? 'selected' : '' }}>
                Reguler (Bayar Lunas)
            </option>

            <option value="tabungan"
                {{ old('tipe_jamaah', $jamaah->tipe_jamaah ?? '') == 'tabungan' ? 'selected' : '' }}>
                Tabungan
            </option>

            <option value="cicilan"
                {{ old('tipe_jamaah', $jamaah->tipe_jamaah ?? '') == 'cicilan' ? 'selected' : '' }}>
                Cicilan
            </option>

        </select>

        <small class="text-muted">
            Digunakan untuk skema pembayaran jamaah
        </small>
    </div>

</div>

{{-- =====================================================
KESEHATAN
===================================================== --}}
<h6 class="fw-bold mb-3">Kesehatan</h6>
<div class="row g-3 mb-4">
@foreach([
 'pernah_umroh' => 'Pernah Umroh',
 'pernah_haji' => 'Pernah Haji',
 'merokok' => 'Merokok',
 'penyakit_khusus' => 'Penyakit Khusus',
 'kursi_roda' => 'Kursi Roda'
] as $field => $label)
    <div class="col-md-4">
        <label>{{ $label }}</label>
        <select name="{{ $field }}"
                class="form-control rounded-pill">
            <option value="Tidak">Tidak</option>
            <option value="Ya"
                {{ old($field, $jamaah->$field ?? '')=='Ya'?'selected':'' }}>
                Ya
            </option>
        </select>
    </div>
@endforeach

    <div class="col-md-6">
        <label>Nama Penyakit</label>
        <input name="nama_penyakit"
               class="form-control rounded-pill"
               value="{{ old('nama_penyakit', $jamaah->nama_penyakit ?? '') }}">
    </div>
</div>

{{-- =====================================================
FOTO & CATATAN
===================================================== --}}
<h6 class="fw-bold mb-3">Lampiran</h6>
<div class="row g-3 mb-4">

    <div class="col-md-6">
        <label>Foto</label>
        <input type="file"
               name="foto"
               class="form-control rounded-pill">
        @if($isEdit && $jamaah->foto)
            <small class="text-muted">Foto lama tersimpan</small>
        @endif
    </div>

    <div class="col-md-6">
        <label>Keterangan</label>
        <textarea name="keterangan"
                  rows="3"
                  class="form-control rounded-4">{{ old('keterangan', $jamaah->keterangan ?? '') }}</textarea>
    </div>

</div>

<button type="submit"
        class="btn btn-success rounded-pill px-4">
    💾 {{ $isEdit ? 'Update Jamaah' : 'Simpan Jamaah' }}
</button>

</div>
</div>
