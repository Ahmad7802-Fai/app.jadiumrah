@extends('layouts.cabang')

@section('title', 'Data Jamaah Cabang')

@section('breadcrumb')
<nav class="cabang-breadcrumb">
    <a href="{{ route('cabang.dashboard') }}" class="crumb-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Jamaah</span>
</nav>
@endsection

@section('content')
<div class="cabang-jamaah">

    {{-- ===============================
       PAGE HEADER
    =============================== --}}
    <div class="page-header mb-16 d-flex justify-between align-center">
        <div>
            <h1 class="page-title">Data Jamaah</h1>
            <p class="page-subtitle">
                Jamaah terdaftar di cabang Anda
            </p>
        </div>

        <a href="{{ route('cabang.jamaah.create') }}"
           class="c-btn primary lg">
            <i class="fas fa-user-plus"></i>
            Tambah Jamaah
        </a>
    </div>

    {{-- ===============================
       FLASH MESSAGE
    =============================== --}}
    @if(session('success'))
        <div class="c-card dense mb-12">
            <span class="c-badge success">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="c-card dense mb-12">
            <span class="c-badge danger">
                {{ session('error') }}
            </span>
        </div>
    @endif

{{-- ===============================
   FILTER
=============================== --}}
<div class="c-card dense mb-16">
    <form method="GET"
          action="{{ route('cabang.jamaah.index') }}"
          class="c-filter">

        {{-- SEARCH --}}
        <div class="c-filter__group">
            <label class="c-filter__label">Cari Jamaah</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   class="c-filter__input"
                   placeholder="Nama / NIK / No HP / No ID">
        </div>

        {{-- AGENT --}}
        <div class="c-filter__group">
            <label class="c-filter__label">Agent</label>
            <select name="agent_id" class="c-filter__input">
                <option value="">Semua Agent</option>
                @foreach($agents as $a)
                    <option value="{{ $a->id }}"
                        @selected(request('agent_id') == $a->id)>
                        {{ $a->user->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ACTION --}}
        <div class="c-filter__actions">
            <button class="c-btn primary sm">
                <i class="fas fa-search"></i>
                Filter
            </button>

            @if(request()->hasAny(['q','agent_id']))
                <a href="{{ route('cabang.jamaah.index') }}"
                   class="c-btn outline sm">
                    Reset
                </a>
            @endif
        </div>

    </form>
</div>

    {{-- ===============================
       MOBILE — CARD LIST
    =============================== --}}
    <div class="d-block d-md-none">
        @forelse($jamaah as $j)
            <div class="c-card dense mb-12">

                <div class="d-flex justify-between align-center">
                    <div>
                        <div class="fw-600">{{ $j->nama_lengkap }}</div>
                        <div class="fs-12 text-muted">
                            {{ $j->no_hp }} · ID {{ $j->no_id }}
                        </div>
                    </div>

                    @if($j->status === 'APPROVED')
                        <span class="c-badge success">APPROVED</span>
                    @else
                        <span class="c-badge warning">
                            {{ strtoupper($j->status) }}
                        </span>
                    @endif
                </div>

                <div class="mt-2 fs-12 text-muted">
                    Agent: {{ $j->agent->user->nama ?? '-' }}
                </div>

                <div class="d-flex gap-6 mt-3">
                    <a href="{{ route('cabang.jamaah.show',$j->id) }}"
                       class="c-btn outline sm">
                        Detail
                    </a>

                    <a href="{{ route('cabang.jamaah.edit',$j->id) }}"
                       class="c-btn sm">
                        Edit
                    </a>
                </div>

            </div>
        @empty
            <div class="c-empty">
                Belum ada data jamaah
            </div>
        @endforelse
    </div>

    {{-- ===============================
       DESKTOP — TABLE
    =============================== --}}
    <div class="d-none d-md-block">
        <div class="c-card">
            <div class="c-table-wrap">
                <table class="c-table is-dense">

                    <thead>
                        <tr>
                            <th>No ID</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Agent</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($jamaah as $j)
                        <tr>

                            <td data-label="No ID" class="fw-600">
                                {{ $j->no_id }}
                            </td>

                            <td data-label="Nama">
                                {{ $j->nama_lengkap }}
                            </td>

                            <td data-label="No HP">
                                {{ $j->no_hp }}
                            </td>

                            <td data-label="Agent">
                                {{ $j->agent->user->nama ?? '-' }}
                            </td>

                            <td data-label="Status">
                                @if($j->status === 'APPROVED')
                                    <span class="c-badge success">APPROVED</span>
                                @else
                                    <span class="c-badge warning">
                                        {{ strtoupper($j->status) }}
                                    </span>
                                @endif
                            </td>

                            <td data-label="Aksi">
                                <div class="cell-actions">
                                    <a href="{{ route('cabang.jamaah.show',$j->id) }}"
                                       class="c-btn icon outline sm"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('cabang.jamaah.edit',$j->id) }}"
                                       class="c-btn icon outline sm"
                                       title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="c-table-empty">
                                    Belum ada data jamaah
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- ===============================
       PAGINATION
    =============================== --}}
    @if($jamaah->hasPages())
        <div class="mt-16 d-flex justify-center">
            {{ $jamaah->links() }}
        </div>
    @endif

</div>
@endsection
