@extends('layouts.agent')

@section('title', 'Dashboard Agent')

@section('content')

{{-- =====================================================
   PAGE HEADER
===================================================== --}}
<div class="mb-4">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">
        Ringkasan performa lead, follow up, dan komisi
    </p>
</div>

{{-- =====================================================
   LEAD STATS
===================================================== --}}
<h4 class="mb-2">Lead</h4>

<div class="card-grid card-grid-stat mb-4">
    <div class="card card-stat">
        <div>Total Lead</div>
        <strong>{{ $kpi['total_lead'] }}</strong>
    </div>

    <div class="card card-stat">
        <div>Lead Aktif</div>
        <strong>{{ $kpi['active_lead'] }}</strong>
    </div>

    <div class="card card-stat">
        <div>Lead Closed</div>
        <strong>{{ $kpi['closing_lead'] }}</strong>
    </div>

    <div class="card card-stat is-highlight">
        <div>Conversion Rate</div>
        <strong>{{ $kpi['conversion_rate'] }}%</strong>
    </div>
</div>

{{-- =====================================================
   FOLLOW UP STATS
===================================================== --}}
<h4 class="mb-2">Follow Up</h4>

<div class="card-grid card-grid-stat mb-4">
    <div class="card card-stat">
        <div>Total Follow Up</div>
        <strong>{{ $kpi['total_followup'] }}</strong>
    </div>

    <div class="card card-stat">
        <div>Hari Ini</div>
        <strong>{{ $kpi['followup_today'] }}</strong>
    </div>

    <div class="card card-stat">
        <div>Terjadwal</div>
        <strong>{{ $kpi['followup_upcoming'] }}</strong>
    </div>
</div>

{{-- =====================================================
   KOMISI STATS
===================================================== --}}
<h4 class="mb-2">Komisi</h4>

<div class="card-grid card-grid-stat mb-4">
    <div class="card card-stat is-highlight">
        <div>Total Komisi</div>
        <strong>
            Rp {{ number_format($kpi['komisi']['total'], 0, ',', '.') }}
        </strong>
    </div>

    <div class="card card-stat">
        <div>Pending</div>
        <strong>
            Rp {{ number_format($kpi['komisi']['pending'], 0, ',', '.') }}
        </strong>
    </div>

    <div class="card card-stat">
        <div>Siap Dicairkan</div>
        <strong>
            Rp {{ number_format($kpi['komisi']['available'], 0, ',', '.') }}
        </strong>
    </div>

    <div class="card card-stat">
        <div>Sudah Dibayar</div>
        <strong>
            Rp {{ number_format($kpi['komisi']['paid'], 0, ',', '.') }}
        </strong>
    </div>
</div>

{{-- =====================================================
   REFERRAL LINK
===================================================== --}}
<h4 class="mb-2">Link Referral Paket Umrah</h4>

<div class="card-stack mb-4">

    @forelse ($links as $item)

        <div class="card card-referral">

            {{-- TITLE --}}
            <div class="font-semibold mb-2">
                {{ $item['paket']->title }}
            </div>

            {{-- INPUT + ACTION --}}
            <div class="card-referral-input">

                <input
                    type="text"
                    value="{{ $item['link'] }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    data-link="{{ $item['link'] }}"
                    onclick="copyReferralLink(this)">
                    Copy Link Referral
                </button>

            </div>

        </div>

    @empty

        <div class="card text-center">
            <div class="text-sm text-muted">
                Belum ada link referral
            </div>
        </div>

    @endforelse

</div>

@endsection

<script>
function copyReferralLink(btn) {
    const link = btn.dataset.link;
    if (!link) return;

    navigator.clipboard.writeText(link).then(() => {
        const originalText = btn.innerText;

        btn.innerText = '✔ Copied!';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        }, 1400);
    });
}
</script>
