@extends('layouts.jamaah')

@section('title','Profil Akun')

@section('content')

{{-- ================= PAGE HEADER ================= --}}
<div class="j-page-title mb-3">
    <h2>Profil Akun</h2>
    <p class="fs-13 text-muted">
        Kelola informasi & keamanan akun Anda
    </p>
</div>

{{-- ================= INFORMASI AKUN ================= --}}
<div class="j-card mb-3">

    <div class="j-card__label mb-2">
        Informasi Akun
    </div>

    <form method="POST"
          action="{{ route('jamaah.profile.update') }}"
          autocomplete="off">
        @csrf

        {{-- NAMA JAMAAH --}}
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text"
                   name="nama_lengkap"
                   value="{{ old('nama_lengkap', $jamaah->nama_lengkap) }}"
                   class="form-input"
                   autocomplete="name"
                   required>
        </div>

        {{-- NO HP --}}
        <div class="form-group mb-2">
            <label class="form-label">No. HP (Login)</label>
            <input type="tel"
                   name="phone"
                   value="{{ old('phone', $user->phone) }}"
                   class="form-input"
                   inputmode="numeric"
                   autocomplete="tel"
                   required>
        </div>

        <button type="submit" class="j-btn primary w-100">
            Simpan Perubahan
        </button>
    </form>

</div>

{{-- ================= KEAMANAN AKUN ================= --}}
<div class="j-card">

    <div class="j-card__label mb-2">
        Keamanan Akun
    </div>

    <form method="POST"
          action="{{ route('jamaah.profile.password') }}"
          autocomplete="off">
        @csrf

        {{-- USERNAME (PASSWORD MANAGER SUPPORT) --}}
        <input type="text"
               name="username"
               value="{{ $user->email ?? $user->phone }}"
               autocomplete="username"
               class="sr-only"
               tabindex="-1">

        <div class="form-group">
            <label class="form-label">Password Lama</label>
            <input type="password"
                   name="current_password"
                   class="form-input"
                   autocomplete="current-password"
                   required>
        </div>

        <div class="form-group">
            <label class="form-label">Password Baru</label>
            <input type="password"
                   name="password"
                   class="form-input"
                   minlength="8"
                   autocomplete="new-password"
                   required>
        </div>

        <div class="form-group mb-2">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-input"
                   autocomplete="new-password"
                   required>
        </div>

        <button type="submit" class="j-btn primary w-100">
            Perbarui Password
        </button>
    </form>

</div>

@endsection
