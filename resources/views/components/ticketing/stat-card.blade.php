@props([
    'title' => '',
    'value' => null,
])

<div class="border rounded-lg p-4 bg-white">
    <div class="text-xs text-gray-500 mb-1">
        {{ $title }}
    </div>

    <div class="text-lg font-semibold">
        {{ $value ?? $slot }}
    </div>
</div>
