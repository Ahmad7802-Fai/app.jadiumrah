@php
    $open  = $open  ?? false;
    $items = $items ?? [];
@endphp

<li class="submenu">
    <a class="sidebar-link submenu-toggle {{ $open ? 'active' : '' }}"
       data-bs-toggle="collapse"
       href="#{{ $id }}"
       role="button"
       aria-expanded="{{ $open ? 'true' : 'false' }}">

        <i class="fas {{ $icon }}"></i>
        <span class="sidebar-text">{{ $label }}</span>
        <i class="fas fa-chevron-right ms-auto"></i>
    </a>

    @if(count($items))
        <ul id="{{ $id }}" class="collapse {{ $open ? 'show' : '' }}">
            @foreach($items as $item)
                @include('layouts.sidebar.components.item', $item)
            @endforeach
        </ul>
    @endif
</li>
