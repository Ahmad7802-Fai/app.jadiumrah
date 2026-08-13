@props([
    'label' => '',
    'route' => '#',
])

@php
    $isActive = $route !== '#' && request()->routeIs($route);
@endphp

<a href="{{ $route !== '#' ? route($route) : '#' }}"
    class="flex items-center gap-3 px-6 py-3 rounded-lg transition-all hover:bg-gray-100
           {{ $isActive ? 'bg-gray-200 font-semibold text-jadigreen' : 'text-gray-700' }}">

    {{ $slot }}
    {{ $label }}

</a>
