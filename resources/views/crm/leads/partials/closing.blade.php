@php
    $pipelineTahap = strtolower($lead->pipeline?->tahap ?? '');
    $closing       = $lead->closing;
@endphp

<div class="card card-hover">

    {{-- ================= HEADER ================= --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Closing</span>

        @if($lead->status === 'CLOSED')
            <span class="badge badge-soft-success">
                CLOSED
            </span>

        @elseif($closing && $closing->status === 'PENDING')
            <span class="badge badge-soft-warning">
                PENDING
            </span>
        @endif
    </div>

    {{-- ================= BODY ================= --}}
    <div class="card-body text-sm">

        {{-- ========== FINAL: CLOSED ========== --}}
        @if($lead->status === 'CLOSED')

            <p class="mb-2">
                Closing telah <strong>disetujui</strong> dan lead dikunci.
            </p>

            <div class="badge badge-soft-success mb-3">
                Closing Approved
            </div>

            <a href="{{ route('crm.closing.show', $closing) }}"
               class="btn btn-outline-primary btn-sm">
                Lihat Dokumen Closing
            </a>

        {{-- ========== PENDING APPROVAL ========== --}}
        @elseif($closing && $closing->status === 'PENDING')

            <p class="mb-2">
                Closing telah diajukan dan sedang menunggu persetujuan admin.
            </p>

            <div class="badge badge-soft-warning mb-3">
                Menunggu Approval
            </div>

            <a href="{{ route('crm.closing.show', $closing) }}"
               class="btn btn-outline-primary btn-sm">
                Lihat Detail Closing
            </a>

        {{-- ========== SIAP AJUKAN ========== --}}
        @elseif($pipelineTahap === 'komit' && !$closing)

            <p class="mb-3">
                Lead sudah berada pada tahap <strong>KOMIT</strong> dan siap diajukan closing.
            </p>

            <form method="POST"
                  action="{{ route('crm.leads.closing.submit', $lead) }}">
                @csrf

                <button type="submit"
                        class="btn btn-primary btn-sm">
                    Ajukan Closing
                </button>
            </form>

        {{-- ========== BELUM KOMIT ========== --}}
        @else

            <p class="text-muted mb-1">
                Closing belum dapat diajukan.
            </p>

            <div class="text-xs text-muted">
                Lead harus berada pada tahap <strong>KOMIT</strong> sebelum closing.
            </div>

        @endif

    </div>
</div>
