@extends('layouts.agent')

@section('page-title','Detail Jamaah')
@section('page-subtitle','Informasi jamaah & pembayaran')

@section('content')

@php
    // =====================================================
    // CORE HITUNGAN (SINKRON PAYMENT SERVICE)
    // =====================================================
    $harga = (int)(
        $jamaah->harga_disepakati
        ?? $jamaah->harga_default
        ?? 0
    );

    $totalBayar = $jamaah->payments
        ->whereIn('status', ['pending','valid'])
        ->sum('jumlah');

    $sisa = max($harga - $totalBayar, 0);

    $isApproved = $jamaah->status === 'approved';

    // 🔒 LOCK JIKA ADA PEMBAYARAN PENDING
    $hasPending = $jamaah->payments
        ->where('status', 'pending')
        ->count() > 0;

    // 🚫 FLAG TABUNGAN (UI GUARD)
    $isTabungan = $jamaah->tipe_jamaah === 'tabungan';
@endphp

{{-- ===============================
FLASH MESSAGE
=============================== --}}
@if(session('success'))
<div class="alert alert-success text-xs mb-3">
    {{ session('success') }}
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning text-xs mb-3">
    {{ session('warning') }}
</div>
@endif

<div class="space-y-4">

{{-- ===============================
HEADER
=============================== --}}
<div class="flex justify-between items-start">
    <div>
        <h2 class="text-sm font-semibold">{{ $jamaah->nama_lengkap }}</h2>
        <div class="text-xs text-gray-500">No ID: {{ $jamaah->no_id }}</div>
    </div>

    <a href="{{ route('agent.jamaah.index') }}"
       class="btn-gray btn-xs">
        Kembali
    </a>
</div>

{{-- ===============================
DATA JAMAAH
=============================== --}}
<div class="card">
    <div class="card-header py-2">
        <h3 class="card-title text-xs">Data Jamaah</h3>
    </div>

    <div class="card-body grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div>
            <div class="label">Nama</div>
            <div class="value">{{ $jamaah->nama_lengkap }}</div>
        </div>
        <div>
            <div class="label">No HP</div>
            <div class="value">{{ $jamaah->no_hp }}</div>
        </div>
        <div>
            <div class="label">Paket</div>
            <div class="value">{{ $jamaah->nama_paket ?? '-' }}</div>
        </div>
    </div>
</div>

{{-- ===============================
PEMBAYARAN
=============================== --}}
<div class="card">
    <div class="card-header py-2 flex justify-between items-center">
        <h3 class="card-title text-xs">Pembayaran</h3>

        {{-- FORM AKTIF HANYA JIKA AMAN --}}
        @if($isApproved && !$hasPending && $harga > 0 && $sisa > 0)
            <button onclick="openPaymentModal()"
                    class="btn-primary btn-xs">
                + Pembayaran
            </button>
        @endif
    </div>

    <div class="card-body grid grid-cols-3 gap-3 text-xs">
        <div>
            <div class="label">Harga</div>
            <div class="value">{{ number_format($harga) }}</div>
        </div>
        <div>
            <div class="label">Dibayar</div>
            <div class="value">{{ number_format($totalBayar) }}</div>
        </div>
        <div>
            <div class="label">Sisa</div>
            <div class="value">{{ number_format($sisa) }}</div>
        </div>
    </div>

    {{-- STATUS --}}
    <div class="px-3 pb-3 space-y-1">

        @if($isTabungan)
            <span class="badge badge-soft-success">
                Tipe Tabungan
            </span>
            <div class="text-[11px] text-gray-500 badge badge-soft-danger">
                Pembayaran dilakukan melalui Top Up Tabungan
            </div>

        @elseif(!$isApproved)
            <span class="badge badge-soft-success">
                Menunggu Approval Pusat
            </span>

        @elseif($hasPending)
            <span class="badge badge-yellow">
                Menunggu Approval Pembayaran
            </span>
            <div class="text-[11px] text-gray-500">
                Input pembayaran dikunci sementara
            </div>

        @elseif($totalBayar === 0)
            <span class="badge badge-yellow">
                Belum Bayar
            </span>

        @elseif($sisa > 0)
            <span class="badge badge-soft-warning">
                Belum Lunas
            </span>

        @else
            <span class="badge badge-soft-success">
                Lunas
            </span>
        @endif

    </div>

</div>

{{-- ===============================
RIWAYAT PEMBAYARAN — DESKTOP
=============================== --}}
<div class="card desktop-only">
    <div class="card-header py-2">
        <h3 class="card-title text-xs">Riwayat Pembayaran</h3>
    </div>

    <div class="card-body p-0">
        <div class="table-wrapper">

            <table class="table">
                <colgroup>
                    <col style="width: 20%">
                    <col style="width: 20%">
                    <col style="width: 25%">
                    <col style="width: 15%">
                </colgroup>

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th class="table-right">Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($jamaah->payments as $p)
                    <tr>
                        <td>{{ optional($p->tanggal_bayar)->format('d M Y') ?? '-' }}</td>

                        <td>{{ ucfirst($p->metode) }}</td>

                        <td class="table-right">
                            Rp {{ number_format($p->jumlah) }}
                        </td>

                        <td>
                            @if($p->status === 'valid')
                                <span class="badge badge-green">Valid</span>
                            @else
                                <span class="badge badge-yellow">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="table-empty">
                            Belum ada pembayaran
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>
{{-- ===============================
RIWAYAT PEMBAYARAN — MOBILE
=============================== --}}
<div class="mobile-only card-grid">

@forelse($jamaah->payments as $p)
    <div class="card">

        <div class="card-row">
            <div>
                <div class="font-semibold text-sm">
                    Rp {{ number_format($p->jumlah) }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ optional($p->tanggal_bayar)->format('d M Y') ?? '-' }}
                </div>
            </div>

            @if($p->status === 'valid')
                <span class="badge badge-green">Valid</span>
            @else
                <span class="badge badge-yellow">Pending</span>
            @endif
        </div>

        <div class="card-stack text-xs mt-2">
            <div class="card-row">
                <span>Metode</span>
                <strong>{{ ucfirst($p->metode) }}</strong>
            </div>
        </div>

    </div>
@empty
    <div class="card text-center text-sm text-gray-500">
        Belum ada pembayaran
    </div>
@endforelse

</div>

{{-- ===============================
MODAL INPUT PEMBAYARAN
=============================== --}}
@if($isApproved && !$hasPending && !$isTabungan && $harga > 0 && $sisa > 0)
<div id="paymentModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div id="paymentModalBox"
         class="bg-white w-full max-w-md rounded-lg shadow-md
                transform scale-95 opacity-0 transition-all">

        {{-- HEADER --}}
        <div class="px-4 py-3 border-b flex justify-between items-center">
            <span class="text-sm font-semibold">Input Pembayaran</span>
            <button type="button" onclick="closePaymentModal()">✕</button>
        </div>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('agent.jamaah.payment', $jamaah->id) }}"
              enctype="multipart/form-data"
              class="form p-4">
            @csrf

            <div class="form-group">
                <label>Tanggal Bayar</label>
                <input type="date"
                       name="tanggal_bayar"
                       value="{{ now()->toDateString() }}"
                       required
                       class="form-input">
            </div>

            <div class="form-group">
                <label>Nominal</label>
                <input type="number"
                       name="amount"
                       min="10000"
                       max="{{ $sisa }}"
                       placeholder="Contoh: 1000000"
                       required
                       class="form-input">
            </div>

            <div class="form-group">
                <label>Metode</label>
                <select name="metode" class="form-select">
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                </select>
            </div>

            <div class="form-group">
                <label>Bukti Transfer</label>
                <input type="file"
                       name="bukti_transfer"
                       class="form-input">
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <input type="text"
                       name="keterangan"
                       placeholder="Opsional"
                       class="form-input">
            </div>

            <div class="form-actions justify-end">
                <button type="button"
                        onclick="closePaymentModal()"
                        class="btn-gray btn-sm">
                    Batal
                </button>
                <button class="btn-primary btn-sm">
                    Simpan Pembayaran
                </button>
            </div>

        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function openPaymentModal(){
    const m = document.getElementById('paymentModal');
    const b = document.getElementById('paymentModalBox');
    m.classList.remove('hidden');
    m.classList.add('flex');
    setTimeout(() => b.classList.remove('scale-95','opacity-0'), 10);
}
function closePaymentModal(){
    const m = document.getElementById('paymentModal');
    const b = document.getElementById('paymentModalBox');
    b.classList.add('scale-95','opacity-0');
    setTimeout(() => {
        m.classList.add('hidden');
        m.classList.remove('flex');
    }, 200);
}
</script>
@endpush
