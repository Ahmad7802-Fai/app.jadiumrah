@props([
    'value' => 0
])

<span class="font-mono">
    Rp {{ number_format($value, 0, ',', '.') }}
</span>
