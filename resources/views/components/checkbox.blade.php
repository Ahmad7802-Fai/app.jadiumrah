{{-- resources/views/components/checkbox.blade.php --}}
@props([
    'label',
    'name',
    'checked' => false,
])

<label class="flex items-center gap-3">
    <input
        type="checkbox"
        name="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
    >
    <span class="text-sm text-gray-700">
        {{ $label }}
    </span>
</label>