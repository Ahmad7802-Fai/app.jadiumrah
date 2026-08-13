@extends('layouts.admin')

@section('title', 'Data Lead')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Data Lead</h1>
        <div class="page-subtitle">
            Kelola & pantau seluruh aktivitas lead
        </div>
    </div>

    <div class="page-header__actions">
        <a href="{{ route('crm.leads.create') }}"
           class="btn btn-primary">
            + Tambah Lead
        </a>
    </div>
</div>

{{-- ======================================================
| FILTER — DESKTOP
====================================================== --}}
<div class="card card-hover d-none d-md-block">
    <div class="card-body">

        <form method="GET"
              action="{{ route('crm.leads.index') }}"
              class="form">

            <div class="form-grid">

                {{-- SEARCH --}}
                <div class="form-group">
                    <label>Cari Lead</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Nama / No HP / Email"
                           class="form-control">
                </div>

                {{-- STATUS --}}
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="NEW"     @selected(request('status') === 'NEW')>NEW</option>
                        <option value="ACTIVE"  @selected(request('status') === 'ACTIVE')>ACTIVE</option>
                        <option value="CLOSED"  @selected(request('status') === 'CLOSED')>CLOSED</option>
                    </select>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Cari
                </button>

                @if(request()->hasAny(['q','status']))
                    <a href="{{ route('crm.leads.index') }}"
                       class="btn btn-light">
                        Reset
                    </a>
                @endif
            </div>

        </form>
    </div>
</div>

{{-- ======================================================
| FILTER — MOBILE
====================================================== --}}
<div class="card card-hover d-block d-md-none">
    <div class="card-body">

        <form method="GET" class="form">

            <div class="form-group">
                <label>Cari Lead</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Nama / No HP / Email"
                       class="form-control">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="NEW" @selected(request('status')==='NEW')>NEW</option>
                    <option value="ACTIVE" @selected(request('status')==='ACTIVE')>ACTIVE</option>
                    <option value="CLOSED" @selected(request('status')==='CLOSED')>CLOSED</option>
                </select>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('crm.leads.index') }}" class="btn btn-light">Reset</a>
            </div>

        </form>
    </div>
</div>

{{-- ======================================================
| MOBILE VIEW — CARD LIST
====================================================== --}}
<div class="d-block d-md-none">
@forelse($leads as $lead)
    <div class="card card-hover mb-3 {{ $lead->isOverdue() ? 'card-overdue-soft' : '' }}">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-semibold">{{ $lead->nama }}</div>
                    <div class="text-muted text-sm">{{ $lead->no_hp }}</div>
                </div>

                <div class="text-end">
                    <span class="badge-status-{{ strtolower($lead->status) }}">
                        {{ strtoupper($lead->status) }}
                    </span>

                    @if($lead->isOverdue())
                        <div class="mt-1">
                            <span class="badge-danger-soft text-xs">
                                ⏰ OVERDUE
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- META --}}
            <div class="text-sm text-muted mb-3">
                <div><strong>Sumber:</strong> {{ optional($lead->sumber)->nama_sumber ?? '-' }}</div>
                <div><strong>Channel:</strong> {{ ucfirst($lead->channel) }}</div>
                <div><strong>Agent:</strong> {{ optional($lead->agent)->nama ?? '-' }}</div>

                @if($lead->latestFollowUp)
                    <div class="mt-1 text-xs">
                        <strong>Next:</strong>
                        {{ optional($lead->latestFollowUp->followup_date)?->format('d M Y H:i') ?? '-' }}
                    </div>
                @endif
            </div>

            {{-- ACTION --}}
            <div class="form-actions">
                <a href="{{ route('crm.leads.show', $lead) }}"
                   class="btn btn-outline-primary btn-sm">
                    Detail
                </a>

                @if(!$lead->isLocked())
                    <a href="{{ route('crm.leads.edit', $lead) }}"
                       class="btn btn-light btn-sm">
                        Edit
                    </a>
                @else
                    <span class="btn btn-light btn-sm disabled">
                        Terkunci
                    </span>
                @endif
            </div>

        </div>
    </div>
@empty
    <div class="empty-state">
        <h4>Belum ada data lead</h4>
        <p>Silakan tambahkan lead baru</p>
    </div>
@endforelse
</div>

{{-- ======================================================
| DESKTOP VIEW — TABLE
====================================================== --}}
<div class="card card-hover d-none d-md-block">
    <div class="table-responsive">

        <table class="table table-compact">

            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Sumber</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Agent</th>
                    <th class="table-right col-actions">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($leads as $lead)
                <tr class="{{ $lead->isOverdue() ? 'row-danger-soft' : '' }}">

                    <td>
                        <div class="fw-semibold">{{ $lead->nama }}</div>
                        <div class="text-muted text-sm">{{ $lead->no_hp }}</div>
                    </td>

                    <td>{{ $lead->no_hp }}</td>
                    <td>{{ optional($lead->sumber)->nama_sumber ?? '-' }}</td>

                    <td>
                        <span class="badge-gray-soft">
                            {{ ucfirst($lead->channel) }}
                        </span>
                    </td>

                    <td>
                        <span class="badge-status-{{ strtolower($lead->status) }}">
                            {{ strtoupper($lead->status) }}
                        </span>

                        @if($lead->isOverdue())
                            <div class="mt-1">
                                <span class="badge-danger-soft text-xs">
                                    ⏰ OVERDUE
                                </span>
                            </div>
                        @endif
                    </td>

                    <td>{{ optional($lead->agent)->nama ?? '-' }}</td>

                    <td class="table-right col-actions">
                        <div class="table-actions">
                            <a href="{{ route('crm.leads.show', $lead) }}"
                               class="btn btn-outline-primary btn-xs">
                                👁
                            </a>

                            @if(!$lead->isLocked())
                                <a href="{{ route('crm.leads.edit', $lead) }}"
                                   class="btn btn-outline-secondary btn-xs">
                                    ✏️
                                </a>
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        Belum ada data lead
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>

    </div>
</div>

{{-- ======================================================
| PAGINATION
====================================================== --}}
<div class="mt-4">
    {{ $leads->links() }}
</div>

@endsection
