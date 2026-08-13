@extends('layouts.admin')

@section('title', 'Tambah Item Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Item Layanan</h1>
            <p class="page-subtitle">
                {{ $master->nama_layanan }}
            </p>
        </div>

        <div class="page-actions">
            <a href="{{ route('keuangan.layanan.show', $master->id) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </div>


    {{-- =====================================================
    | FORM CARD
    ===================================================== --}}
    <div class="card card-hover">
        <div class="card-body card-body-lg">

            <form action="{{ route('keuangan.layanan.items.store') }}" method="POST">
                @csrf

                <input type="hidden"
                       name="id_layanan_master"
                       value="{{ $master->id }}">

                {{-- ROW 1 --}}
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Item</label>
                        <input type="text"
                               name="nama_item"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipe</label>
                        <select name="tipe"
                                class="form-select"
                                id="tipeSelect">
                            <option value="default">Default</option>
                            <option value="hotel">Hotel (per-night)</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-none"
                         id="durasiWrapper">
                        <label class="form-label">
                            Durasi Hari Default
                        </label>
                        <input type="number"
                               name="durasi_hari_default"
                               class="form-control"
                               min="1"
                               placeholder="Contoh: 3">
                    </div>

                </div>


                {{-- ROW 2 --}}
                <div class="row g-3 mt-1">

                    <div class="col-md-3">
                        <label class="form-label">Harga</label>
                        <input type="number"
                               name="harga"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Satuan</label>
                        <input type="text"
                               name="satuan"
                               class="form-control"
                               value="unit">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <input type="text"
                               name="vendor"
                               class="form-control">
                    </div>

                </div>


                {{-- ROW 3 --}}
                <div class="row g-3 mt-1">

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date"
                               name="tanggal_mulai"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date"
                               name="tanggal_selesai"
                               class="form-control">
                    </div>

                </div>


                {{-- ACTION --}}
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>
                        Simpan Item
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>


{{-- =====================================================
| SCRIPT
===================================================== --}}
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const tipe   = document.getElementById('tipeSelect');
    const durasi = document.getElementById('durasiWrapper');

    function toggleDurasi() {
        durasi.classList.toggle('d-none', tipe.value !== 'hotel');
    }

    toggleDurasi();
    tipe.addEventListener('change', toggleDurasi);
});
</script>
@endsection
 --}}

{{-- ========================================= --}}
{{-- JAVASCRIPT: Toggle input durasi --}}
{{-- ========================================= --}}
<script>
document.addEventListener("DOMContentLoaded", function(){

    // mobile
    const tipeMobile = document.querySelector(".tipe-select");
    const durasiMobile = document.querySelector(".durasi-wrapper");

    if (tipeMobile) {
        tipeMobile.onchange = function () {
            durasiMobile.style.display =
                this.value === "hotel" ? "block" : "none";
        };
    }

    // desktop
    const tipeDesktop = document.querySelector(".tipe-select-desktop");
    const durasiDesktop = document.querySelector(".durasi-desktop");

    if (tipeDesktop) {
        tipeDesktop.onchange = function () {
            durasiDesktop.style.display =
                this.value === "hotel" ? "block" : "none";
        };
    }
});
</script>

@endsection
