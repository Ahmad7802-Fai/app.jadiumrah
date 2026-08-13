@php
    $href = $url ?? '#';
    $active = isset($route) && request()->routeIs($route);
@endphp

<a href="{{ $href }}" class="sidebar-link {{ $active ? 'active' : '' }}">
    <i class="fas {{ $icon }}"></i>
    <span class="sidebar-text">{{ $label }}</span>

    @isset($badge)
        <span class="badge bg-danger ms-auto">{{ $badge }}</span>
    @endisset
</a>
