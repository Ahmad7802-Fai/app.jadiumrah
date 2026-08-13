@extends('layouts.cabang')

@section('title', 'Edit Lead')

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

        <h1 class="page-title">Edit Lead Cabang</h1>
        <p class="page-subtitle">
            Perbarui data lead sebelum diajukan closing
        </p>
    </div>
</div>

{{-- =====================================================
   FORM CARD
===================================================== --}}
<div class="c-card max-w-520">

    <form method="POST"
          action="{{ route('cabang.leads.update', $lead) }}"
          class="c-form">
        @csrf
        @method('PUT')

        {{-- ================= NAMA ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                Nama Lengkap
            </label>
            <input
                type="text"
                name="nama"
                class="c-form__input"
                required
                value="{{ old('nama', $lead->nama) }}">
        </div>

        {{-- ================= NO HP ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                No HP
            </label>
            <input
                type="text"
                name="no_hp"
                class="c-form__input"
                required
                value="{{ old('no_hp', $lead->no_hp) }}">
        </div>

        {{-- ================= EMAIL ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                Email <span class="text-muted">(opsional)</span>
            </label>
            <input
                type="email"
                name="email"
                class="c-form__input"
                value="{{ old('email', $lead->email) }}">
        </div>

        {{-- ================= SUMBER ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                Sumber Lead
            </label>
            <select name="sumber_id"
                    class="c-form__input"
                    required>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('sumber_id', $lead->sumber_id) == $s->id)>
                        {{ $s->nama_sumber }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ================= CHANNEL ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                Channel
            </label>
            <select name="channel" class="c-form__input">
                <option value="offline"
                    @selected(old('channel', $lead->channel)==='offline')>
                    Offline
                </option>
                <option value="online"
                    @selected(old('channel', $lead->channel)==='online')>
                    Online
                </option>
            </select>
        </div>

        {{-- ================= CATATAN ================= --}}
        <div class="c-form__group">
            <label class="c-form__label">
                Catatan
            </label>
            <textarea
                name="catatan"
                class="c-form__textarea"
                placeholder="Catatan tambahan terkait lead">{{ old('catatan', $lead->catatan) }}</textarea>
        </div>

        {{-- ================= LOCK INFO ================= --}}
        @if($lead->status === 'CLOSED')
            <div class="c-alert danger">
                Lead sudah <strong>CLOSED</strong> dan tidak dapat diubah.
            </div>
        @endif

        {{-- ================= ACTION ================= --}}
        <div class="d-flex gap-8 pt-8">

            @if($lead->status !== 'CLOSED')
                <button class="c-btn primary">
                    💾 Simpan Perubahan
                </button>
            @endif

            <a href="{{ route('cabang.leads.index') }}"
               class="c-btn outline">
                Kembali
            </a>
        </div>

    </form>

</div>

@endsection
