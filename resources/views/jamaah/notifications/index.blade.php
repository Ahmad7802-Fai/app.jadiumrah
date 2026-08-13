@extends('layouts.jamaah')

@section('title','Notifikasi')

@section('content')

{{-- ================= PAGE TITLE ================= --}}
<div class="j-page-title">
    <h2>Notifikasi</h2>
    <p>Informasi & update terbaru untuk Anda</p>
</div>

{{-- ================= EMPTY ================= --}}
@if($notifs->isEmpty())
    <div class="j-empty">
        <i class="fas fa-bell-slash"></i>
        <p>Belum ada notifikasi</p>
        <small>
            Semua informasi penting terkait tabungan dan umrah Anda
            akan muncul di sini
        </small>
    </div>
@else

    {{-- ================= LIST ================= --}}
    <div class="j-notif-list">
        @foreach($notifs as $n)
            <div class="j-notif {{ $n->is_read ? 'read' : 'unread' }}">
                
                {{-- indicator --}}
                @if(!$n->is_read)
                    <span class="j-notif-dot"></span>
                @endif

                <div class="j-notif-content">
                    <div class="j-notif-title">
                        {{ $n->title }}
                    </div>

                    <div class="j-notif-message">
                        {{ $n->message }}
                    </div>

                    <div class="j-notif-time">
                        {{ optional($n->created_at)->format('d M Y • H:i') ?? '—' }}
                    </div>

                </div>
            </div>
        @endforeach
    </div>

@endif

@endsection
