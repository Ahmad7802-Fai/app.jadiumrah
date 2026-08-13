{{-- resources/views/components/select.blade.php --}}
@props([
    'label' => null,
    'name',
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                        text-sm text-gray-800 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                        transition'
        ]) }}
    >
        {{ $slot }}
    </select>
</div>