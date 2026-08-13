@extends('layouts.agent')

@section('page-title','Tambah Lead')
@section('page-subtitle','Input calon jamaah baru')

@section('content')

<form method="POST"
      action="{{ route('agent.leads.store') }}"
      class="card-stack">
    @csrf

    {{-- =====================================================
       INFORMASI LEAD
    ===================================================== --}}
    <div class="card card-stack">

        <div class="fw-semibold">
            Informasi Lead
        </div>

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input
                type="text"
                name="nama"
                class="form-control"
                value="{{ old('nama') }}"
                placeholder="Nama calon jamaah"
                required
            >
            @error('nama')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>No. HP / WhatsApp</label>
            <input
                type="text"
                name="no_hp"
                class="form-control"
                value="{{ old('no_hp') }}"
                placeholder="08xxxxxxxxxx"
                required
            >
            @error('no_hp')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Email <span class="text-muted">(Opsional)</span></label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                placeholder="email@contoh.com"
            >
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

    </div>

    {{-- =====================================================
       SUMBER LEAD
    ===================================================== --}}
    <div class="card card-stack">

        <div class="fw-semibold">
            Sumber Lead
        </div>

        <div class="form-group">
            <label>Sumber</label>
            <select name="sumber_id" class="form-select" required>
                <option value="">— Pilih Sumber —</option>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}" @selected(old('sumber_id')==$s->id)>
                        {{ $s->nama_sumber }}
                    </option>
                @endforeach
            </select>
            @error('sumber_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Channel</label>
            <select name="channel" class="form-select" required>
                <option value="">— Pilih Channel —</option>
                <option value="online"  @selected(old('channel')=='online')>Online</option>
                <option value="offline" @selected(old('channel')=='offline')>Offline</option>
            </select>
            @error('channel')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

    </div>

    {{-- =====================================================
       CATATAN
    ===================================================== --}}
    <div class="card card-stack">

        <div class="fw-semibold">
            Catatan Awal
        </div>

        <div class="form-group">
            <label>Catatan</label>
            <textarea
                name="catatan"
                class="form-control"
                rows="4"
                placeholder="Catatan awal tentang lead ini">{{ old('catatan') }}</textarea>
        </div>

    </div>

    {{-- =====================================================
       ACTION
    ===================================================== --}}
    <div class="card card-row">

        <button type="submit" class="btn btn-primary">
            Simpan Lead
        </button>

        <a href="{{ route('agent.leads.index') }}"
           class="btn btn-secondary">
            Batal
        </a>

    </div>

</form>

@endsection
