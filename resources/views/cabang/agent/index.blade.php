@extends('layouts.cabang')

@section('title', 'Agent Cabang')

@section('breadcrumb')
<nav class="cabang-breadcrumb">
    <a href="{{ route('cabang.dashboard') }}" class="crumb-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Agent</span>
</nav>
@endsection

@section('content')
<div class="cabang-agent">

    {{-- =====================================================
       PAGE HEADER
    ===================================================== --}}
    <div class="page-header">

        {{-- DESKTOP --}}
        <div class="justify-between align-center">
            <div>
                <h1 class="page-title">Agent Cabang</h1>
                <p class="page-subtitle">
                    Daftar sales / agent di cabang Anda
                </p>
            </div>

            <a href="{{ route('cabang.agent.create') }}"
               class="c-btn primary lg">
                <i class="fas fa-user-tie"></i>
                Tambah Agent
            </a>
        </div>

        {{-- MOBILE --}}
        <div class="d-block d-md-none">
            <h1 class="page-title mb-6">Agent Cabang</h1>

            <a href="{{ route('cabang.agent.create') }}"
               class="c-btn primary w-full">
                <i class="fas fa-user-tie"></i>
                Tambah Agent
            </a>
        </div>

    </div>

    {{-- =====================================================
       FLASH MESSAGE
    ===================================================== --}}
    @if(session('success'))
        <div class="c-card dense mb-12">
            <span class="c-badge success">
                {{ session('success') }}
            </span>
        </div>
    @endif

    {{-- =====================================================
       FILTER
    ===================================================== --}}
    <div class="c-card dense mb-16">
        <form method="GET"
              action="{{ route('cabang.agent.index') }}"
              class="c-filter">

            {{-- SEARCH --}}
            <div class="c-filter__group">
                <label class="c-filter__label">Cari Agent</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       class="c-filter__input"
                       placeholder="Nama / Email / Kode Agent">
            </div>

            {{-- STATUS --}}
            <div class="c-filter__group">
                <label class="c-filter__label">Status</label>
                <select name="status" class="c-filter__input">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>
                        Aktif
                    </option>
                    <option value="0" @selected(request('status') === '0')>
                        Nonaktif
                    </option>
                </select>
            </div>

            {{-- ACTION --}}
            <div class="c-filter__actions">
                <button class="c-btn primary sm">
                    <i class="fas fa-search"></i>
                    Filter
                </button>

                @if(request()->hasAny(['q','status']))
                    <a href="{{ route('cabang.agent.index') }}"
                       class="c-btn outline sm">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- =====================================================
       MOBILE — CARD LIST
    ===================================================== --}}
    <div class="d-block d-md-none">

        @forelse($agents as $agent)
            <div class="c-card dense mb-12">

                <div class="d-flex justify-between align-center">
                    <div>
                        <div class="fw-600">
                            {{ $agent->user->nama ?? '-' }}
                        </div>
                        <div class="fs-12 text-muted">
                            {{ $agent->kode_agent }} · {{ $agent->user->email ?? '-' }}
                        </div>
                    </div>

                    @if($agent->is_active)
                        <span class="c-badge success">AKTIF</span>
                    @else
                        <span class="c-badge danger">NONAKTIF</span>
                    @endif
                </div>

                <div class="mt-2 fs-12 text-muted">
                    Komisi: {{ number_format($agent->komisi_persen, 2) }}%
                </div>

                <div class="d-flex gap-6 mt-3">

                    <a href="{{ route('cabang.agent.show', $agent->id) }}"
                       class="c-btn outline sm w-full">
                        Detail
                    </a>

                    <form method="POST"
                          action="{{ route('cabang.agent.toggle', $agent->id) }}"
                          class="w-full"
                          onsubmit="return confirm('Ubah status agent ini?')">
                        @csrf
                        @method('PATCH')

                        <button class="c-btn sm w-full {{ $agent->is_active ? 'danger' : 'success' }}">
                            {{ $agent->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                </div>

            </div>
        @empty
            <div class="c-empty">
                Belum ada agent
            </div>
        @endforelse

    </div>

    {{-- =====================================================
       DESKTOP — TABLE
    ===================================================== --}}
    <div class="d-none d-md-block">
        <div class="c-card">
            <div class="c-table-wrap">
                <table class="c-table is-dense">

                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Agent</th>
                            <th>Email</th>
                            <th>Komisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($agents as $agent)
                        <tr>

                            <td class="fw-600">
                                {{ $agent->kode_agent }}
                            </td>

                            <td>
                                {{ $agent->user->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $agent->user->email ?? '-' }}
                            </td>

                            <td>
                                {{ number_format($agent->komisi_persen, 2) }}%
                            </td>

                            <td>
                                @if($agent->is_active)
                                    <span class="c-badge success">AKTIF</span>
                                @else
                                    <span class="c-badge danger">NONAKTIF</span>
                                @endif
                            </td>

                            <td>
                                <div class="cell-actions">

                                    <a href="{{ route('cabang.agent.show', $agent->id) }}"
                                       class="c-btn icon outline sm"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('cabang.agent.edit', $agent->id) }}"
                                       class="c-btn icon outline sm"
                                       title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('cabang.agent.toggle', $agent->id) }}"
                                          onsubmit="return confirm('Ubah status agent ini?')">
                                        @csrf
                                        @method('PATCH')

                                        <button class="c-btn icon outline sm"
                                                title="{{ $agent->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="fas {{ $agent->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="c-table-empty">
                                    Belum ada agent
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- =====================================================
       PAGINATION
    ===================================================== --}}
    @if($agents->hasPages())
        <div class="mt-16 d-flex justify-center">
            {{ $agents->links() }}
        </div>
    @endif

</div>
@endsection
