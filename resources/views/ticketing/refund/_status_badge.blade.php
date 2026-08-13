@php
    $status = $status ?? $refund->approval_status ?? null;
@endphp

@switch($status)
    @case('PENDING')
        <span class="badge badge-warning">PENDING</span>
        @break

    @case('APPROVED')
        <span class="badge badge-success">APPROVED</span>
        @break

    @case('REJECTED')
        <span class="badge badge-danger">REJECTED</span>
        @break

    @case('EXECUTED')
        <span class="badge badge-primary">EXECUTED</span>
        @break

    @default
        <span class="badge badge-secondary">UNKNOWN</span>
@endswitch
