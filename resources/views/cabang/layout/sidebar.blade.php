<aside id="sidebar">

    {{-- HEADER --}}
<div class="sidebar-header">
    <img
        src="{{ asset('logo.png') }}"
        alt="Logo Cabang"
        class="sidebar-logo"
    >
    <span class="sidebar-text"></span>
</div>


    {{-- MENU --}}
    <nav>

        <a href="{{ route('cabang.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('cabang.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="{{ route('cabang.leads.index') }}"
           class="sidebar-link {{ request()->routeIs('cabang.leads.index') ? 'active' : '' }}">
            <i class="fas fa-bullseye"></i>
            <span class="sidebar-text">Leads</span>
        </a>

        {{-- <a href="{{ route('cabang.leads.kanban') }}"
           class="sidebar-link {{ request()->routeIs('cabang.leads.kanban') ? 'active' : '' }}">
            <i class="fas fa-columns"></i>
            <span class="sidebar-text">Pipeline</span>
        </a> --}}

        <a href="{{ route('cabang.jamaah.index') }}"
           class="sidebar-link {{ request()->routeIs('cabang.jamaah.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span class="sidebar-text">Jamaah</span>
        </a>

        <a href="{{ route('cabang.agent.index') }}"
           class="sidebar-link {{ request()->routeIs('cabang.agent.*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span class="sidebar-text">Agent</span>
        </a>

    </nav>
</aside>
