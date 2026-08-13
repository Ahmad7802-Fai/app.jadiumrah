@extends('layouts.website')

@section('title', 'Pendaftaran Umrah')

@section('content')
<div class="form-page">

    <div class="form-card">

        {{-- HEADER --}}
        <div class="form-header">
            <h1>Pendaftaran Umrah</h1>
            <p>Isi data singkat, tim kami akan menghubungi Anda via WhatsApp</p>
        </div>

        {{-- REFERRAL (HIDDEN BY CSS) --}}
        <div class="referral-box">
            🤝 Direkomendasikan oleh Agen
            <strong>{{ $referral['kode_agent'] }}</strong>
        </div>

        <form method="POST" action="{{ route('website.daftar.store') }}">
            @csrf

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="nama_lengkap"
                    value="{{ old('nama_lengkap') }}"
                    placeholder="Contoh: Ahmad Faizi"
                    required
                >
                @error('nama_lengkap')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>No HP / WhatsApp</label>
                <input
                    type="text"
                    name="no_hp"
                    value="{{ old('no_hp') }}"
                    placeholder="08xxxxxxxxxx"
                    required
                >
                @error('no_hp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Email (Opsional)</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                >
            </div>

            <div class="form-group">
                <label>Kota Domisili</label>
                <input
                    type="text"
                    name="kota"
                    value="{{ old('kota') }}"
                    placeholder="Jakarta"
                >
            </div>

            <div class="form-group">
                <label>Jumlah Jamaah</label>
                <select name="jumlah">
                    <option value="1">1 Orang</option>
                    <option value="2">2 Orang</option>
                    <option value="3">3 Orang</option>
                    <option value="4">4 Orang</option>
                    <option value="5+">5+ Orang</option>
                </select>
            </div>

            <div class="form-group">
                <label>Catatan (Opsional)</label>
                <textarea
                    name="catatan"
                    placeholder="Pertanyaan / permintaan khusus"
                >{{ old('catatan') }}</textarea>
            </div>

            <button type="submit" class="btn-primary btn-block">
                Daftar Sekarang
            </button>

            <div class="form-note">
                Dengan mendaftar, Anda setuju dihubungi oleh tim kami via WhatsApp
            </div>
        </form>

    </div>

</div>
@endsection
