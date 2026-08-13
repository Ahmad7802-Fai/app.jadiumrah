<aside class="sidebar">

    {{-- BRAND --}}
    <div class="sidebar-brand">
        <img
            src="{{ asset('assets/images/logo.png') }}"
            alt="Umrah Baik"
            class="sidebar-brand-logo"
        >

        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">Umrah Baik</span>
            <span class="sidebar-brand-sub">Agent Panel</span>
        </div>
    </div>

    {{-- MENU --}}
    <nav class="sidebar-menu">

        <a href="{{ route('agent.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('agent.dashboard') ? 'is-active' : '' }}">
            <span class="icon">📊</span>
            <span class="label">Dashboard</span>
        </a>

        <a href="{{ route('agent.leads.index') }}"
           class="sidebar-link {{ request()->routeIs('agent.leads.*') ? 'is-active' : '' }}">
            <span class="icon">🎯</span>
            <span class="label">Lead</span>
        </a>

        <a href="{{ route('agent.jamaah.index') }}"
           class="sidebar-link {{ request()->routeIs('agent.jamaah.*') ? 'is-active' : '' }}">
            <span class="icon">👥</span>
            <span class="label">Jamaah</span>
        </a>

        <a href="{{ route('agent.komisi.index') }}"
           class="sidebar-link {{ request()->routeIs('agent.komisi.*') ? 'is-active' : '' }}">
            <span class="icon">💰</span>
            <span class="label">Komisi</span>
        </a>

        <div class="sidebar-divider"></div>

    </nav>

    {{-- FOOTER / PROFILE --}}
    <div class="sidebar-footer">
        <a href="{{ route('agent.profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('agent.profile.*') ? 'is-active' : '' }}">
            <span class="icon">👤</span>
            <span class="label">Profil</span>
        </a>
    </div>

</aside>
