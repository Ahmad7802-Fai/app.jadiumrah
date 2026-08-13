@props(['label', 'value' => null])

<div>
    <div class="text-gray-500 text-xs mb-1">
        {{ $label }}
    </div>
    <div class="font-medium text-gray-900">
        {{ $value ?? $slot }}
    </div>
</div>
