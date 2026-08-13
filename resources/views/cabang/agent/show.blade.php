@extends('layouts.cabang')

@section('title', 'Detail Agent')

@section('content')

{{-- =====================================================
| PAGE HEADER
===================================================== --}}
<div class="page-header mb-16">
    <div>
        <a href="{{ route('cabang.agent.index') }}"
           class="c-btn outline sm mb-6">
            ← Kembali
        </a>

        <h1 class="page-title">Detail Agent</h1>
        <p class="page-subtitle">
            Informasi lengkap sales / agent cabang
        </p>
    </div>
</div>

{{-- =====================================================
| INFORMASI AGENT
===================================================== --}}
<div class="c-card has-header-bg mb-16">

    <div class="c-card__header">
        Informasi Agent
    </div>

    <div class="c-card__body">

        <div class="c-info-grid">

            <div class="c-info-item">
                <div class="c-info-label">Kode Agent</div>
                <div class="c-info-value">
                    {{ $agent->kode_agent }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Nama</div>
                <div class="c-info-value">
                    {{ $agent->user->nama }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Email</div>
                <div class="c-info-value">
                    {{ $agent->user->email }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">No HP</div>
                <div class="c-info-value">
                    {{ $agent->phone ?? '-' }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Komisi</div>
                <div class="c-info-value">
                    {{ number_format($agent->komisi_persen, 2) }}%
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Status</div>
                <div class="c-info-value">
                    <span class="c-badge {{ $agent->is_active ? 'success' : 'danger' }}">
                        {{ $agent->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Dibuat</div>
                <div class="c-info-value text-muted">
                    {{ $agent->created_at->format('d M Y H:i') }}
                </div>
            </div>

        </div>

    </div>
</div>

{{-- =====================================================
| ACTIONS
===================================================== --}}
<div class="c-card mb-16">
    <div class="c-card__body d-flex gap-8 flex-wrap">

        <a href="{{ route('cabang.agent.edit', $agent->id) }}"
           class="c-btn primary sm">
            ✏️ Edit Agent
        </a>

        <form method="POST"
              action="{{ route('cabang.agent.toggle', $agent->id) }}"
              onsubmit="return confirm('Ubah status agent ini?')">
            @csrf
            @method('PATCH')

            <button class="c-btn outline sm">
                {{ $agent->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>

    </div>
</div>

{{-- =====================================================
| RINGKASAN AGENT
===================================================== --}}
<div class="c-card has-header-bg mb-16">

    <div class="c-card__header">
        Ringkasan Agent
    </div>

    <div class="c-card__body">

        <div class="c-info-grid">

            <div class="c-info-item">
                <div class="c-info-label">Total Jamaah</div>
                <div class="c-info-value">
                    {{ $agent->jamaah_count ?? 0 }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Total Lead</div>
                <div class="c-info-value">
                    {{ $agent->lead_count ?? 0 }}
                </div>
            </div>

            <div class="c-info-item">
                <div class="c-info-label">Status Agent</div>
                <div class="c-info-value">
                    <span class="c-badge {{ $agent->is_active ? 'success' : 'danger' }}">
                        {{ $agent->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
