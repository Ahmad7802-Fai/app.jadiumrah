@extends('layouts.jamaah')

@section('title','Riwayat Top Up')

@section('content')

<h2 class="mb-3">Riwayat Top Up</h2>

@if($topups->isEmpty())
    <div class="text-muted fs-13">
        Belum ada riwayat top up.
    </div>
@else

<div class="timeline timeline--compact">

@foreach($topups as $row)
    @php
        $statusClass = match($row->status) {
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
                    Rp {{ number_format($row->amount,0,',','.') }}
                </strong>

                <span class="badge badge-{{ $statusClass }}">
                    {{ $row->status }}
                </span>
            </div>

            {{-- TANGGAL --}}
            <div class="timeline-meta">
                {{ $row->transfer_date?->format('d M Y') }}
            </div>

            {{-- BUKTI --}}
            @if($row->proof_file)
                <div class="mt-1">
                    <a href="{{ asset('storage/'.$row->proof_file) }}"
                       target="_blank"
                       class="fs-12 text-primary">
                        Lihat bukti transfer
                    </a>
                </div>
            @endif

            {{-- CATATAN ADMIN --}}
            @if($row->admin_note)
                <div class="fs-12 text-muted mt-1">
                    Catatan: {{ $row->admin_note }}
                </div>
            @endif

        </div>
    </div>
@endforeach

</div>
@endif

<div class="mt-4">
    <a href="{{ route('jamaah.dashboard') }}"
       class="fs-13 text-primary">
        ← Kembali ke Dashboard
    </a>
</div>

@endsection
