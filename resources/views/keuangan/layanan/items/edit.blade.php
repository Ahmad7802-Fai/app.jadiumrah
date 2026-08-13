@extends('layouts.admin')

@section('title', 'Edit Item Layanan')

@section('content')
<div class="page-container">

    {{-- =====================================================
    | PAGE HEADER
    ===================================================== --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Item Layanan</h1>
            <p class="page-subtitle">
                {{ $item->nama_item }}
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

            <form action="{{ route('keuangan.layanan.items.update', $item->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                {{-- ROW 1 --}}
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Item</label>
                        <input type="text"
                               name="nama_item"
                               value="{{ $item->nama_item }}"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipe</label>
                        <select name="tipe"
                                class="form-select"
                                id="tipeSelect">
                            <option value="default" @selected($item->tipe === 'default')>
                                Default
                            </option>
                            <option value="hotel" @selected($item->tipe === 'hotel')>
                                Hotel (per-night)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-none"
                         id="durasiWrapper">
                        <label class="form-label">
                            Durasi Hari Default
                        </label>
                        <input type="number"
                               name="durasi_hari_default"
                               value="{{ $item->durasi_hari_default }}"
                               class="form-control"
                               min="1">
                    </div>

                </div>


                {{-- ROW 2 --}}
                <div class="row g-3 mt-1">

                    <div class="col-md-3">
                        <label class="form-label">Harga</label>
                        <input type="number"
                               name="harga"
                               value="{{ $item->harga }}"
                               class="form-control"
                               min="0"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Satuan</label>
                        <input type="text"
                               name="satuan"
                               value="{{ $item->satuan }}"
                               class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <input type="text"
                               name="vendor"
                               value="{{ $item->vendor }}"
                               class="form-control">
                    </div>

                </div>


                {{-- ROW 3 --}}
                <div class="row g-3 mt-1">

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date"
                               name="tanggal_mulai"
                               value="{{ $item->tanggal_mulai }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date"
                               name="tanggal_selesai"
                               value="{{ $item->tanggal_selesai }}"
                               class="form-control">
                    </div>

                </div>


                {{-- ACTION --}}
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>
                        Update Item
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
@endsection --}}

{{-- ===================================================== --}}
{{-- JS: toggle hotel days --}}
{{-- ===================================================== --}}
<script>
document.addEventListener("DOMContentLoaded", function(){

    // MOBILE
    const tipeMobile = document.querySelector(".tipe-mobile");
    const durasiMobile = document.querySelector(".durasi-mobile");

    if (tipeMobile) {
        tipeMobile.onchange = function () {
            durasiMobile.style.display = this.value === "hotel" ? "block" : "none";
        };
    }

    // DESKTOP
    const tipeDesktop = document.querySelector(".tipe-desktop");
    const durasiDesktop = document.querySelector(".durasi-desktop");

    if (tipeDesktop) {
        tipeDesktop.onchange = function () {
            durasiDesktop.style.display = this.value === "hotel" ? "block" : "none";
        };
    }
});
</script>

@endsection
