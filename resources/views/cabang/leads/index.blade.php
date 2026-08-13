@extends('layouts.cabang')

@section('title', 'Data Lead Cabang')

@section('breadcrumb')
<nav class="cabang-breadcrumb">
    <a href="{{ route('cabang.dashboard') }}" class="crumb-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="crumb-sep">/</span>
    <span class="crumb-current">Leads</span>
</nav>
@endsection

@section('content')
<div class="cabang-leads">

    {{-- ===============================
       PAGE HEADER
    =============================== --}}
    <div class="page-header mb-16 d-flex justify-between align-center">
        <div>
            <h1 class="page-title">Data Lead Cabang</h1>
            <p class="page-subtitle">
                Kelola lead cabang & aktivitas follow up
            </p>
        </div>

        <a href="{{ route('cabang.leads.create') }}"
           class="c-btn primary lg">
            <i class="fas fa-plus"></i>
            Tambah Lead
        </a>
    </div>

    {{-- ===============================
   FILTER
=============================== --}}
<div class="c-card dense mb-16">
    <form method="GET"
          action="{{ route('cabang.leads.index') }}"
          class="c-filter">

        {{-- SEARCH --}}
        <div class="c-filter__group">
            <label class="c-filter__label">Cari</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   class="c-filter__input"
                   placeholder="Nama / No HP">
        </div>

        {{-- STATUS --}}
        <div class="c-filter__group">
            <label class="c-filter__label">Status</label>
            <select name="status" class="c-filter__input">
                <option value="">Semua</option>
                <option value="NEW" @selected(request('status')==='NEW')>
                    NEW
                </option>
                <option value="ACTIVE" @selected(request('status')==='ACTIVE')>
                    ACTIVE
                </option>
                <option value="CLOSED" @selected(request('status')==='CLOSED')>
                    CLOSED
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
                <a href="{{ route('cabang.leads.index') }}"
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
        @forelse($leads as $lead)
            <div class="c-card dense mb-12">

                <div class="d-flex justify-between align-center">
                    <div>
                        <div class="fw-600">{{ $lead->nama }}</div>
                        <div class="text-muted fs-12">{{ $lead->no_hp }}</div>
                    </div>

                    @if($lead->status === 'NEW')
                        <span class="c-badge warning">NEW</span>
                    @elseif($lead->status === 'ACTIVE')
                        <span class="c-badge success">ACTIVE</span>
                    @else
                        <span class="c-badge danger">CLOSED</span>
                    @endif
                </div>

                <div class="mt-2 fs-12 text-muted">
                    <div>Sumber: {{ optional($lead->sumber)->nama_sumber ?? '-' }}</div>
                    <div>Agent: {{ optional($lead->agent)->nama ?? '-' }}</div>
                </div>

                <div class="d-flex gap-6 mt-3">
                    <a href="{{ route('cabang.leads.show',$lead) }}"
                       class="c-btn outline sm">
                        Detail
                    </a>

                    @if($lead->status !== 'CLOSED')
                        <a href="{{ route('cabang.leads.edit',$lead) }}"
                           class="c-btn sm">
                            Edit
                        </a>
                    @else
                        <span class="c-badge">Terkunci</span>
                    @endif
                </div>

            </div>
        @empty
            <div class="c-empty">
                Belum ada data lead
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
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Sumber</th>
                            <th>Status</th>
                            <th>Agent</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($leads as $lead)
                        <tr>

                            <td data-label="Nama">
                                <div class="fw-600">{{ $lead->nama }}</div>
                                <div class="text-muted fs-12">{{ $lead->no_hp }}</div>
                            </td>

                            <td data-label="No HP">
                                {{ $lead->no_hp }}
                            </td>

                            <td data-label="Sumber">
                                {{ optional($lead->sumber)->nama_sumber ?? '-' }}
                            </td>

                            <td data-label="Status">
                                @if($lead->status === 'NEW')
                                    <span class="c-badge warning">NEW</span>
                                @elseif($lead->status === 'ACTIVE')
                                    <span class="c-badge success">ACTIVE</span>
                                @else
                                    <span class="c-badge danger">CLOSED</span>
                                @endif
                            </td>

                            <td data-label="Agent">
                                {{ optional($lead->agent)->nama ?? '-' }}
                            </td>

                            <td data-label="Aksi">
                                <div class="cell-actions">
                                    <a href="{{ route('cabang.leads.show',$lead) }}"
                                       class="c-btn icon outline sm"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($lead->status !== 'CLOSED')
                                        <a href="{{ route('cabang.leads.edit',$lead) }}"
                                           class="c-btn icon outline sm"
                                           title="Edit">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @else
                                        <span class="c-badge">Terkunci</span>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="c-table-empty">
                                    Belum ada data lead
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
    @if($leads->hasPages())
        <div class="mt-16 d-flex justify-center">
            {{ $leads->links() }}
        </div>
    @endif

</div>
@endsection
