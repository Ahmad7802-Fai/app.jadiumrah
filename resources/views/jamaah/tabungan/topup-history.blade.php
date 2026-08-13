@extends('layouts.jamaah')

@section('title','Riwayat Top Up')

@section('content')

<h2 class="mb-3">Riwayat Top Up</h2>

@if($topups->isEmpty())
    <div class="j-card j-card--soft text-center text-muted fs-13">
        Belum ada riwayat top up
    </div>
@else

<div class="timeline timeline--compact">

@foreach($topups as $t)
    @php
        $statusClass = match ($t->status) {
            'APPROVED' => 'success',
            'PENDING'  => 'warning',
            default    => 'danger',
        };
    @endphp

    <div class="timeline-item">

        {{-- DOT STATUS --}}
        <div class="timeline-dot {{ $statusClass }}"></div>

        {{-- CONTENT --}}
        <div class="timeline-content">

            {{-- NOMINAL + STATUS --}}
            <div class="d-flex justify-between align-center">
                <strong>
                    Rp {{ number_format($t->amount,0,',','.') }}
                </strong>

                <span class="badge badge-{{ $statusClass }}">
                    {{ $t->status }}
                </span>
            </div>

            {{-- TANGGAL --}}
            <div class="timeline-meta">
                {{ $t->transfer_date?->format('d M Y') }}
            </div>

        </div>
    </div>
@endforeach

</div>
@endif

<div class="mt-4">
    <a href="{{ route('jamaah.dashboard') }}"
       class="btn btn-secondary btn-block">
        ← Kembali ke Dashboard
    </a>
</div>

@endsection
