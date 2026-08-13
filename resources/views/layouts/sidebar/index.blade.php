<aside class="app-sidebar">

    {{-- LOGO --}}
    <div class="sidebar-header">
        <img src="{{ asset('logo.png') }}" class="sidebar-logo-auto">
    </div>

    {{-- MENU --}}
    <ul class="sidebar-menu">

        {{-- DASHBOARD --}}
        @include('layouts.sidebar.components.item', [
            'url'   => route('dashboard'),
            'route' => 'dashboard',
            'icon'  => 'fa-home',
            'label' => 'Dashboard'
        ])

        {{-- ROLE BASED --}}
        @if(Auth::user()->role === 'SUPERADMIN')
            @include('layouts.sidebar._superadmin')
        @endif

        @if(in_array(Auth::user()->role, ['SUPERADMIN','ADMIN']))
            @include('layouts.sidebar._admin')
        @endif

        @if(in_array(Auth::user()->role, ['SUPERADMIN','OPERATOR']))
            @include('layouts.sidebar._operator')
        @endif

        @if(in_array(Auth::user()->role, ['SUPERADMIN','KEUANGAN']))
            @include('layouts.sidebar._keuangan')
        @endif

        @if(in_array(Auth::user()->role, ['SUPERADMIN','INVENTORY']))
            @include('layouts.sidebar._inventory')
        @endif

        @if(in_array(Auth::user()->role, ['SUPERADMIN','SALES']))
            @include('layouts.sidebar._sales')
        @endif

    </ul>

    {{-- LOGOUT --}}
    <div class="sidebar-logout">
        <a href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidebar-link">
            <i class="fas fa-sign-out-alt"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>

</aside>

{{-- OVERLAY (MOBILE) --}}
<div id="sidebar-overlay" class="app-overlay"></div>
