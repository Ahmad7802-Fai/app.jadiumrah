@extends('layouts.admin')

@section('title','Audit Log')

@section('content')

<div class="page page--narrow">

    {{-- ======================================================
    | PAGE HEADER
    ====================================================== --}}
    <div class="page-header mb-md">
        <div>
            <div class="page-title">Audit Log Ticketing</div>
            <div class="text-sm text-muted">
                Riwayat aktivitas dan perubahan data ticketing
            </div>
        </div>
    </div>

    {{-- ======================================================
    | TABLE : AUDIT LOG
    ====================================================== --}}
    <div class="card card-hover">

        <div class="card-header">
            <div class="card-title">Audit Log</div>
        </div>

        <div class="card-body p-0">
            <div class="table-wrap">
                <table class="table table-compact">

                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Entity</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>IP</th>
                            <th class="col-actions">Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($logs as $log)
                        <tr>

                            {{-- WAKTU --}}
                            <td class="text-xs text-muted">
                                {{ optional($log->created_at)->format('d M Y H:i') ?? '-' }}
                            </td>

                            {{-- ENTITY --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $log->entity_type }}
                                </div>
                                <div class="text-xs text-muted mono">
                                    #{{ $log->entity_id }}
                                </div>
                            </td>

                            {{-- ACTION --}}
                                <td>
                                    <span class="text-xs fw-semibold px-sm py-xs rounded"
                                        @class([
                                            'badge badge-soft-danger text-danger' => in_array($log->action, ['ISSUE','CANCEL','REFUND']),
                                            'badge badge-soft-warning text-warning' => $log->action === 'UPDATE',
                                            'badge badge-soft-gray text-gray-600' => !in_array($log->action, ['ISSUE','CANCEL','REFUND','UPDATE']),
                                        ])
                                    >
                                        {{ $log->action }}
                                    </span>
                                </td>


                            {{-- USER --}}
                            <td class="text-xs">
                                <div class="fw-semibold">
                                    {{ $log->actor_role ?? 'SYSTEM' }}
                                </div>
                                <div class="text-muted">
                                    ID: {{ $log->actor_id ?? '-' }}
                                </div>
                            </td>

                            {{-- IP --}}
                            <td class="text-xs mono text-muted">
                                {{ $log->ip_address ?? '-' }}
                            </td>

                            {{-- DETAIL --}}
                            <td class="text-right col-actions">
                                @if($log->before || $log->after)
                                    <details>
                                        <summary class="link-primary text-xs cursor-pointer">
                                            View
                                        </summary>

                                        <pre class="mt-xs p-sm bg-dark text-success text-xs rounded overflow-x-auto">
BEFORE:
{{ json_encode($log->before, JSON_PRETTY_PRINT) }}

AFTER:
{{ json_encode($log->after, JSON_PRETTY_PRINT) }}
                                        </pre>
                                    </details>
                                @else
                                    <span class="text-muted text-xs">-</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                Tidak ada audit log.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>

    {{-- ======================================================
    | PAGINATION
    ====================================================== --}}
    <div class="mt-md">
        {{ $logs->links() }}
    </div>

</div>

@endsection
