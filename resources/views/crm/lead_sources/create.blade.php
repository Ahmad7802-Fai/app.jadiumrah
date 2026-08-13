@extends('layouts.admin')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-4">
    <a href="{{ request('redirect', route('crm.leads.create')) }}"
       class="btn-link-ju btn-sm">
        ← Kembali
    </a>

    <h1 class="card-title-premium mt-2">
        Tambah Sumber Lead
    </h1>
</div>

{{-- ================= FORM CARD ================= --}}
<div class="card-sectioned">

    {{-- HEADER --}}
    <div class="card-sectioned-header">
        Informasi Sumber
    </div>

    {{-- BODY --}}
    <div class="card-sectioned-body">

        <form action="{{ route('crm.lead-sources.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                {{-- NAMA SUMBER --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Sumber</label>
                    <input
                        type="text"
                        name="nama_sumber"
                        class="form-control"
                        required
                        placeholder="Contoh: Instagram Ads"
                    >
                </div>

                {{-- TIPE --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>

                {{-- PLATFORM --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Platform</label>
                    <input
                        type="text"
                        name="platform"
                        class="form-control"
                        placeholder="Contoh: Instagram, Facebook, Referensi"
                    >
                </div>

                {{-- LOKASI --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input
                        type="text"
                        name="lokasi"
                        class="form-control"
                        placeholder="Contoh: Jakarta, Bandung"
                    >
                </div>

                {{-- KETERANGAN --}}
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea
                        name="keterangan"
                        rows="3"
                        class="form-control"
                        placeholder="Catatan tambahan (opsional)"
                    ></textarea>
                </div>

            </div>

            {{-- ACTION --}}
            <div class="mt-4 d-flex justify-content-end gap-2">

                <a
                    href="{{ request('redirect', route('crm.leads.create')) }}"
                    class="btn-ju-secondary btn-sm btn-pill">
                    Tutup
                </a>

                <button
                    type="submit"
                    class="btn btn-primary">
                    Simpan Sumber
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
