@extends('layouts.cabang')

@section('title', 'Tambah Lead Cabang')

@section('content')

{{-- =====================================================
   PAGE HEADER
===================================================== --}}
<div class="page-header mb-16">
    <div>
        <a href="{{ route('cabang.leads.index') }}"
           class="c-btn outline sm mb-6">
            ← Kembali
        </a>

        <h1 class="page-title">Tambah Lead Cabang</h1>
        <p class="page-subtitle">
            Input lead baru untuk ditindaklanjuti oleh cabang
        </p>
    </div>
</div>

{{-- =====================================================
   FORM CARD
===================================================== --}}
<div class="c-card has-header-bg">

    <div class="c-card__header">
        Form Lead
    </div>

    <form method="POST"
          action="{{ route('cabang.leads.store') }}"
          class="c-form">
        @csrf

        {{-- ================= NAMA ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">Nama Lengkap *</label>
            <input
                type="text"
                name="nama"
                class="c-form__input"
                required
                value="{{ old('nama') }}"
                placeholder="Nama calon jamaah">
        </div>

        {{-- ================= NO HP ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">No HP *</label>
            <input
                type="text"
                name="no_hp"
                class="c-form__input"
                required
                value="{{ old('no_hp') }}"
                placeholder="08xxxxxxxxxx">
        </div>

        {{-- ================= EMAIL ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">Email</label>
            <input
                type="email"
                name="email"
                class="c-form__input"
                value="{{ old('email') }}"
                placeholder="email@contoh.com">
        </div>

        {{-- ================= SUMBER ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">Sumber Lead *</label>
            <select name="sumber_id"
                    class="c-form__input"
                    required>
                <option value="">— Pilih Sumber —</option>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('sumber_id') == $s->id)>
                        {{ $s->nama_sumber }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ================= CHANNEL ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">Channel</label>
            <select name="channel" class="c-form__input">
                <option value="offline"
                    @selected(old('channel') === 'offline')>
                    Offline
                </option>
                <option value="online"
                    @selected(old('channel') === 'online')>
                    Online
                </option>
            </select>
        </div>

        {{-- ================= CATATAN ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">Catatan</label>
            <textarea
                name="catatan"
                rows="3"
                class="c-form__textarea"
                placeholder="Minat paket, bulan keberangkatan, dll">{{ old('catatan') }}</textarea>
        </div>

        {{-- ================= ACTION ================= --}}
        <div class="d-flex gap-8 mt-12">
            <button class="c-btn primary">
                💾 Simpan Lead
            </button>

            <a href="{{ route('cabang.leads.index') }}"
               class="c-btn outline">
                Batal
            </a>
        </div>

    </form>

</div>

@endsection
