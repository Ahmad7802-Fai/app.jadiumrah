@extends('layouts.admin')

@section('title', 'Closing Menunggu Approval')

@section('content')

{{-- ======================================================
| PAGE HEADER
====================================================== --}}
<div class="page-header">
    <div class="page-header__title">
        <h1>Closing Menunggu Approval</h1>
        <div class="page-subtitle">
            Daftar closing dari sales yang perlu direview
        </div>
    </div>
</div>

{{-- ======================================================
| MOBILE VIEW — CARD LIST
====================================================== --}}
<div class="d-block d-md-none">
@forelse($closings as $c)
    <div class="card card-hover mb-3">
        <div class="card-body">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-semibold">
                        {{ $c->lead->nama }}
                    </div>
                    <div class="text-muted text-sm">
                        Pipeline:
                        {{ ucfirst($c->lead->pipeline?->tahap ?? '-') }}
                    </div>
                </div>

                <span class="badge badge-soft-warning">
                    {{ strtoupper($c->status ?? 'DRAFT') }}
                </span>
            </div>

            {{-- META --}}
            <div class="text-sm text-muted mb-3">
                <div>
                    <strong>Nominal DP:</strong>
                    Rp {{ number_format($c->nominal_dp ?? 0,0,',','.') }}
                </div>
                <div>
                    <strong>Diajukan:</strong>
                    {{ optional($c->created_at)->format('d M Y H:i') }}
                </div>
            </div>

            {{-- ACTION --}}
            <div class="form-actions">
                <a href="{{ route('crm.closing.show', $c) }}"
                   class="btn btn-primary btn-sm">
                    Review Closing
                </a>
            </div>

        </div>
    </div>
@empty
    <div class="empty-state">
        <h4>Tidak ada closing</h4>
        <p>Belum ada closing yang menunggu approval</p>
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
                    <th>Lead</th>
                    <th>Pipeline</th>
                    <th>Nominal DP</th>
                    <th>Status</th>
                    <th class="table-right col-actions">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($closings as $c)
                <tr>

                    {{-- LEAD --}}
                    <td>
                        <div class="fw-semibold">
                            {{ $c->lead->nama }}
                        </div>
                        <div class="text-muted text-sm">
                            {{ optional($c->created_at)->format('d M Y H:i') }}
                        </div>
                    </td>

                    {{-- PIPELINE --}}
                    <td>
                        <span class="badge badge-soft-secondary">
                            {{ ucfirst($c->lead->pipeline?->tahap ?? '-') }}
                        </span>
                    </td>

                    {{-- NOMINAL --}}
                    <td class="fw-semibold">
                        Rp {{ number_format($c->nominal_dp ?? 0,0,',','.') }}
                    </td>

                    {{-- STATUS --}}
                    <td>
                        <span class="badge badge-soft-warning">
                            {{ strtoupper($c->status ?? 'DRAFT') }}
                        </span>
                    </td>

                    {{-- ACTION --}}
                    <td class="table-right col-actions">
                        <div class="table-actions">
                            <a href="{{ route('crm.closing.show', $c) }}"
                               class="btn btn-outline-primary btn-xs"
                               title="Review Closing">
                                👁
                            </a>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="table-empty">
                        Tidak ada closing menunggu approval
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
    {{ $closings->links() }}
</div>

@endsection
