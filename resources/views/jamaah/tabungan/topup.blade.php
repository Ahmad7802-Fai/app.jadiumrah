@extends('layouts.jamaah')

@section('title','Top Up Tabungan')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="j-page-title mb-3">
    <h2>Top Up Tabungan</h2>
    <p class="fs-13 text-muted">
        Masukkan data setoran tabungan umrah Anda
    </p>
</div>

{{-- ================= ERROR VALIDATION ================= --}}
@if ($errors->any())
    <div class="j-card j-card--warning fs-13">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ route('jamaah.tabungan.topup.store') }}"
      enctype="multipart/form-data"
      id="formTopup">

    @csrf

    {{-- ================= NOMINAL ================= --}}
    <div class="j-card">
        <label class="j-card__label">Nominal Setoran</label>
        <input type="number"
               name="amount"
               class="j-input"
               placeholder="Contoh: 1000000"
               min="100000"
               required>
        <div class="j-card__hint">
            Minimal Rp 100.000
        </div>
    </div>

    {{-- ================= TANGGAL ================= --}}
    <div class="j-card">
        <label class="j-card__label">Tanggal Transfer</label>
        <input type="date"
               name="transfer_date"
               class="j-input"
               required>
    </div>

    {{-- ================= BANK ================= --}}
    <div class="j-card">
        <label class="j-card__label">Bank Pengirim</label>
        <select name="bank_sender"
                class="j-input"
                required>
            <option value="">Pilih Bank</option>
            <option>BCA</option>
            <option>BNI</option>
            <option>BRI</option>
            <option>Mandiri</option>
            <option>BSI</option>
            <option>Lainnya</option>
        </select>
    </div>

    {{-- ================= BUKTI TRANSFER ================= --}}
    <div class="j-card">
        <label class="j-card__label">Bukti Transfer</label>

        <label class="j-upload">
            <input type="file"
                   name="proof_file"
                   id="proofInput"
                   accept="image/*"
                   capture="environment"
                   required>

            <div class="j-upload__box">
                <i class="fas fa-camera"></i>
                <span>Pilih / Ambil Foto</span>
            </div>
        </label>

        <div class="j-card__hint">
            Gambar, maksimal 3MB
        </div>
    </div>

    {{-- ================= PREVIEW ================= --}}
    <div id="previewWrapper" class="j-card j-card--soft" style="display:none">
        <label class="j-card__label">Preview Bukti</label>

        <img id="previewImage"
             src=""
             class="j-image-preview">

        <button type="button"
                class="btn btn-outline btn-sm mt-2"
                id="btnRemovePreview">
            Ganti Foto
        </button>
    </div>

    {{-- ================= SUBMIT ================= --}}
    <button type="submit"
            class="btn btn-primary btn-block mt-3"
            id="btnSubmit">

        <span id="btnText">Kirim Top Up</span>
        <span id="btnLoading" style="display:none">
            ⏳ Mengirim...
        </span>
    </button>

</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('formTopup');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    const input = document.getElementById('proofInput');
    const previewWrapper = document.getElementById('previewWrapper');
    const previewImage = document.getElementById('previewImage');
    const btnRemove = document.getElementById('btnRemovePreview');

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            this.value = '';
            return;
        }

        if (file.size > 3 * 1024 * 1024) {
            alert('Ukuran foto maksimal 3MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewWrapper.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    btnRemove.addEventListener('click', function () {
        input.value = '';
        previewImage.src = '';
        previewWrapper.style.display = 'none';
    });

    form.addEventListener('submit', function () {
        btnSubmit.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
    });

});
</script>
@endpush
