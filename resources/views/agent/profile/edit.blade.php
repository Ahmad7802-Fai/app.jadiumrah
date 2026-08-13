@extends('layouts.agent')

@section('page-title','Profil Agent')
@section('page-subtitle','Lengkapi data profil, rekening, dan keamanan akun')

@section('content')

<form method="POST" action="{{ route('agent.profile.update') }}">
@csrf

{{-- ===============================
| FLASH MESSAGE
=============================== --}}
@if(session('success'))
    <div class="alert alert-success mb-4 text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- ===============================
| DATA PROFIL
=============================== --}}
<div class="card max-w-xl mb-4">
    <div class="card-stack">

        <h3 class="font-semibold text-sm">Data Profil</h3>

        <div class="card-stack">
            <div>
                <label class="text-sm">Nama Lengkap</label>
                <input type="text"
                       name="nama"
                       class="form-input"
                       value="{{ old('nama', $agent->nama) }}"
                       required>
            </div>

            <div>
                <label class="text-sm">No. HP</label>
                <input type="text"
                       name="phone"
                       class="form-input"
                       value="{{ old('phone', $agent->phone) }}"
                       required>
            </div>

            <div>
                <label class="text-sm">Email (akun login)</label>
                <input type="email"
                       class="form-input"
                       value="{{ $agent->user->email }}"
                       disabled>
            </div>
        </div>

    </div>
</div>

{{-- ===============================
| INFORMASI AGENT (READ ONLY)
=============================== --}}
<div class="card max-w-xl mb-4">
    <div class="card-stack">

        <h3 class="font-semibold text-sm">Informasi Agen</h3>

        <div class="card-stack text-sm">
            <div class="card-row">
                <span class="text-muted">Kode Agen</span>
                <strong>{{ $agent->kode_agent }}</strong>
            </div>

            <div class="card-row">
                <span class="text-muted">Cabang</span>
                <strong>{{ optional($agent->branch)->nama_cabang ?? '-' }}</strong>
            </div>

            <div class="card-row">
                <span class="text-muted">Komisi Default</span>
                <strong>{{ number_format($agent->komisi_persen, 2) }}%</strong>
            </div>

            <div class="card-row">
                <span class="text-muted">Bergabung</span>
                <strong>{{ $agent->created_at->format('d M Y') }}</strong>
            </div>
        </div>

    </div>
</div>

{{-- ===============================
| DATA REKENING
=============================== --}}
<div class="card max-w-xl mb-4">
    <div class="card-stack">

        <h3 class="font-semibold text-sm">
            Data Rekening (Pencairan Komisi)
        </h3>

        <div class="card-stack">
            <div>
                <label class="text-sm">Nama Bank</label>
                <input type="text"
                       name="bank_name"
                       class="form-input"
                       placeholder="Contoh: BCA, BRI, Mandiri"
                       value="{{ old('bank_name', $agent->bank_name) }}"
                       required>
            </div>

            <div>
                <label class="text-sm">No. Rekening</label>
                <input type="text"
                       name="bank_account_number"
                       class="form-input"
                       value="{{ old('bank_account_number', $agent->bank_account_number) }}"
                       required>
            </div>

            <div>
                <label class="text-sm">Atas Nama</label>
                <input type="text"
                       name="bank_account_name"
                       class="form-input"
                       value="{{ old('bank_account_name', $agent->bank_account_name) }}"
                       required>
            </div>
        </div>

    </div>
</div>

{{-- ===============================
| GANTI PASSWORD (OPSIONAL)
=============================== --}}
<div class="card max-w-xl mb-4">
    <div class="card-stack">

        <h3 class="font-semibold text-sm">Ganti Password</h3>

        <div class="card-stack">
            <div>
                <label class="text-sm">Password Lama</label>
                <input type="password"
                       name="current_password"
                       class="form-input"
                       placeholder="Masukkan password lama">
            </div>

            <div>
                <label class="text-sm">Password Baru</label>
                <input type="password"
                       name="password"
                       class="form-input"
                       placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label class="text-sm">Konfirmasi Password Baru</label>
                <input type="password"
                       name="password_confirmation"
                       class="form-input"
                       placeholder="Ulangi password baru">
            </div>
        </div>

        <p class="text-xs text-muted">
            Kosongkan jika tidak ingin mengganti password.
        </p>

    </div>
</div>

{{-- ===============================
| ACTION (PENUTUP FORM)
=============================== --}}
<div class="card max-w-xl">
    <div class="card-actions">
        <button type="submit" class="btn btn-primary w-full">
            💾 Simpan Perubahan
        </button>
    </div>
</div>

</form>

@endsection
