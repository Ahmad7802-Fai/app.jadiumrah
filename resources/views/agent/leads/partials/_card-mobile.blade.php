@php
$statusClass = match(strtoupper($lead->status)) {
    'NEW'       => 'badge-new',
    'PROSPECT'  => 'badge-prospect',
    'FOLLOWUP'  => 'badge-followup',
    'MEETING'   => 'badge-meeting',
    'KOMIT'     => 'badge-komit',
    'CLOSING'   => 'badge-closing',
    'CLOSED'    => 'badge-closed',
    'LOST'      => 'badge-lost',
    default     => 'badge-gray',
};
@endphp
<div class="card card-stack">

    {{-- TITLE --}}
    <div class="fw-semibold">
        {{ $lead->nama }}
    </div>

    {{-- STATUS --}}
    <div>
        <span class="badge {{ $statusClass }}">
            {{ strtoupper($lead->status) }}
        </span>
    </div>

    {{-- META --}}
    <div class="text-sm text-muted">
        {{ $lead->no_hp }} · {{ $lead->created_at->format('d M Y') }}
    </div>

    {{-- ALERT --}}
    @if($lead->isOverdue())
        <div class="text-sm text-danger fw-semibold">
            ⚠ Follow up terlambat
        </div>
    @endif

    {{-- ACTION --}}
    <div class="card-actions">
        <a href="{{ route('agent.leads.show', $lead) }}"
           class="btn btn-primary btn-block">
            Kelola Lead
        </a>
    </div>

</div>
