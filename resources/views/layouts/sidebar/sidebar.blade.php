<aside class="app-sidebar">
    {{-- HEADER --}}
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('logo.png') }}"
                class="sidebar-logo-auto"
                alt="Logo Umrah Baik">
        </div>
    </div>
    
    {{-- NAV --}}
    <nav class="sidebar-nav">

        {{-- DASHBOARD --}}
        @include('layouts.sidebar._dashboard')

        {{-- SUPERADMIN --}}
        @if(auth()->user()->role === 'SUPERADMIN')
            @include('layouts.sidebar._superadmin')
        @endif

        {{-- ADMIN --}}
        @if(in_array(auth()->user()->role, ['SUPERADMIN','ADMIN']))
            @include('layouts.sidebar._admin')
        @endif

        {{-- OPERATOR --}}
        @if(in_array(auth()->user()->role, ['SUPERADMIN','OPERATOR']))
            @include('layouts.sidebar._operator')
        @endif

        {{-- KEUANGAN --}}
        @if(in_array(auth()->user()->role, ['SUPERADMIN','KEUANGAN']))
            @include('layouts.sidebar._keuangan')
        @endif

        {{-- INVENTORY --}}
        @if(in_array(auth()->user()->role, ['SUPERADMIN','INVENTORY']))
            @include('layouts.sidebar._inventory')
        @endif

        {{-- SALES --}}
        @if(in_array(auth()->user()->role, ['SUPERADMIN','SALES']))
            @include('layouts.sidebar._sales')
        @endif

    </nav>

    {{-- FOOTER --}}
    @include('layouts.sidebar._logout')
</aside>
