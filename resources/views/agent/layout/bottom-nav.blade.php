<nav class="bottom-nav">

    {{-- DASHBOARD --}}
    <a href="{{ route('agent.dashboard') }}"
       class="{{ request()->routeIs('agent.dashboard') ? 'is-active' : '' }}">
        <i class="fa-solid fa-chart-line"></i>
        <span>Dashboard</span>
    </a>

    {{-- LEAD --}}
    <a href="{{ route('agent.leads.index') }}"
       class="{{ request()->routeIs('agent.leads.*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-bullseye"></i>
        <span>Lead</span>
    </a>

    {{-- JAMAAH --}}
    <a href="{{ route('agent.jamaah.index') }}"
       class="{{ request()->routeIs('agent.jamaah.*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-users"></i>
        <span>Jamaah</span>
    </a>

    {{-- KOMISI --}}
    <a href="{{ route('agent.komisi.index') }}"
       class="{{ request()->routeIs('agent.komisi.*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-sack-dollar"></i>
        <span>Komisi</span>
    </a>

    {{-- PROFILE --}}
    <a href="{{ route('agent.profile.edit') }}"
       class="{{ request()->routeIs('agent.profile.*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-user"></i>
        <span>Profil</span>
    </a>

</nav>
