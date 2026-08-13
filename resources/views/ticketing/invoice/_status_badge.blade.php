@php
$map = [
    'UNPAID'    => ['badge-soft-danger',    'Unpaid'],
    'PARTIAL'   => ['badge-soft-warning',   'Partial'],
    'PAID'      => ['badge-soft-success',   'Paid'],
    'CANCELLED' => ['badge-soft-secondary', 'Cancelled'],
];

[$variant, $label] = $map[$status] ?? [
    'badge-soft-secondary',
    ucfirst(strtolower(str_replace('_', ' ', $status)))
];
@endphp

<span class="badge {{ $variant }}">
    {{ $label }}
</span>
