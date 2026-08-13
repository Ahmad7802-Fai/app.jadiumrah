@extends('layouts.website')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<div class="form-page">

    <div class="form-card text-center">

        <h1 class="text-xl font-semibold text-green-600 mb-2">
            ✅ Pendaftaran Berhasil
        </h1>

        <p class="text-sm text-gray-600 mb-4">
            Terima kasih telah mendaftar.<br>
            Tim kami akan segera menghubungi Anda via WhatsApp.
        </p>

        <div class="referral-box" style="display:block">
            Direkomendasikan oleh Agen
            <strong>{{ $referral['kode_agent'] }}</strong>
        </div>

        @if($from)
            <a href="{{ $from }}" class="btn-primary mt-4">
                Kembali ke Paket Umrah
            </a>
        @endif

    </div>

</div>
@endsection
