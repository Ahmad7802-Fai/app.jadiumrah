@extends('layouts.website')

@section('title', $paket->title)

@section('content')
<div class="website-body">

    {{-- ===============================
        HERO SECTION
    =============================== --}}
    <section class="hero"
        style="
            background-image: url('{{ $paket->photo
                ? asset('storage/'.$paket->photo)
                : asset('images/hero-default.jpg') }}');
        "
    >
        <div class="container">
            <div class="hero-content">

                <h1>{{ $paket->title }}</h1>

                <p>
                    Program Umrah pilihan terbaik untuk Anda & keluarga
                    dengan pelayanan amanah dan profesional.
                </p>

                {{-- CTA HERO (DESKTOP ONLY) --}}
<a href="{{ route('website.daftar.create') }}"
   class="btn-primary cta-desktop">
   Daftar Sekarang
</a>


            </div>
        </div>
    </section>

    {{-- ===============================
       INFORMASI PAKET
    =============================== --}}
    <section class="container mt-4 mb-4">
        <div class="paket-card">

            <div class="paket-title">
                Informasi Paket Umrah
            </div>

            <div class="paket-meta">
                📅 Keberangkatan:
                <strong>
                    {{ \Carbon\Carbon::parse($paket->tglberangkat)->translatedFormat('d F Y') }}
                </strong>
            </div>

            <div class="paket-meta">
                ⏱️ Durasi:
                <strong>{{ $paket->durasi }} Hari</strong>
            </div>

            <div class="paket-meta">
                ✈️ Maskapai:
                <strong>{{ $paket->pesawat }}</strong>
                <span class="text-muted">({{ $paket->flight }})</span>
            </div>

            <div class="paket-meta">
                🏨 Hotel Mekkah:
                <strong>{{ $paket->hotmekkah }}</strong>
                <span class="text-muted">(⭐ {{ $paket->rathotmekkah }})</span>
            </div>

            <div class="paket-meta">
                🏨 Hotel Madinah:
                <strong>{{ $paket->hotmadinah }}</strong>
                <span class="text-muted">(⭐ {{ $paket->rathotmadinah }})</span>
            </div>

            <div class="paket-meta">
                🗺️ Program Tambahan:
                <strong>
                    {{ $paket->thaif === 'Ya' ? 'Thaif' : '' }}
                    {{ $paket->dubai === 'Ya' ? ' · Dubai' : '' }}
                    {{ $paket->kereta === 'Ya' ? ' · Kereta Cepat' : '' }}
                </strong>
            </div>

            <div class="paket-meta price">
                💰 Harga mulai:
                <strong>
                    Rp {{ number_format($paket->quad, 0, ',', '.') }}
                </strong>
            </div>

        </div>
    </section>

    {{-- ===============================
        DESKRIPSI
    =============================== --}}
    <section class="container mb-4">
        <div class="paket-card">

            <div class="paket-title">
                Deskripsi Program
            </div>

            <div class="paket-meta">
                {!! nl2br(e($paket->deskripsi ?? 'Deskripsi belum tersedia')) !!}
            </div>

            {{-- CTA DESKRIPSI (DESKTOP ONLY) --}}
            <a href="{{ route('website.daftar.create') }}"
               class="btn-primary cta-desktop mt-4">
                Daftar Umrah Sekarang
            </a>

        </div>
    </section>

    {{-- ===============================
        STICKY CTA (MOBILE ONLY)
    =============================== --}}
<div class="sticky-cta">
    <a href="{{ route('website.daftar.create') }}" class="btn-primary">
        Daftar Umrah
    </a>
</div>


</div>
@endsection
