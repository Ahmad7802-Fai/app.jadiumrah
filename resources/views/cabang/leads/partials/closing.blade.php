@php
    $closing = $lead->closing;
@endphp

<div class="card-sectioned">

    {{-- ======================================================
    | HEADER
    ====================================================== --}}
    <div class="card-sectioned-header d-flex justify-content-between align-items-center">
        <span>Closing</span>

        @if($lead->status === 'CLOSED')
            <span class="badge-status-approved">CLOSED</span>
        @elseif($closing)
            <span class="badge-status-pending">DIAJUKAN</span>
        @else
            <span class="badge-gray-soft">BELUM</span>
        @endif
    </div>

    {{-- ======================================================
    | BODY
    ====================================================== --}}
    <div class="card-sectioned-body">

        {{-- ================= FINAL / LOCKED ================= --}}
        @if($lead->status === 'CLOSED' && $closing)

            <p class="text-sm mb-2">
                Closing telah <strong>disetujui</strong>.
                Lead ini terkunci dan siap diproses.
            </p>

            <div class="badge-success-soft mb-3">
                ✔ Closing Approved
            </div>

            <a href="{{ route('cabang.closing.show', $closing) }}"
               class="btn-ju-outline btn-sm">
                📄 Lihat Dokumen Closing
            </a>

        {{-- ================= SUBMITTED / WAITING ================= --}}
        @elseif($closing)

            <p class="text-sm mb-2">
                Closing telah diajukan dan
                <strong>menunggu approval pusat</strong>.
            </p>

            <div class="badge-warning-soft mb-3">
                ⏳ Menunggu Persetujuan
            </div>

            <a href="{{ route('cabang.closing.show', $closing) }}"
               class="btn-ju-outline btn-sm">
                Lihat Detail Closing
            </a>

        {{-- ================= EMPTY ================= --}}
        @else

            <p class="text-sm text-muted mb-1">
                Closing belum diajukan.
            </p>

            <p class="text-xs text-muted">
                Menunggu proses dari sales atau pusat.
            </p>

        @endif

    </div>
</div>
