@php
$map = [
    'ON_FLOW'   => ['badge-soft-secondary', 'On Flow'],
    'CONFIRMED' => ['badge-soft-warning',   'Confirmed'],
    'ISSUED'    => ['badge-soft-success',   'Issued'],
    'CANCELLED' => ['badge-soft-danger',    'Cancelled'],
];

[$variant, $label] = $map[$status] ?? [
    'badge-soft-secondary',
    ucfirst(strtolower($status))
];
@endphp

<span class="badge {{ $variant }}">
    {{ $label }}
</span>
