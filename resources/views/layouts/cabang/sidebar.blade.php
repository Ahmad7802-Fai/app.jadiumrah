<div class="c-sidebar__logo">
    <img src="{{ asset('logo.png') }}" height="32">
</div>

<nav class="c-sidebar__menu">

    <a href="{{ route('cabang.dashboard') }}"
       class="c-sidebar__link {{ request()->routeIs('cabang.dashboard') ? 'is-active' : '' }}">
        <i class="fas fa-chart-line"></i>
        Dashboard
    </a>

    <a href="{{ route('cabang.leads.index') }}"
       class="c-sidebar__link {{ request()->routeIs('cabang.leads.*') ? 'is-active' : '' }}">
        <i class="fas fa-bullhorn"></i>
        Leads
    </a>

    <a href="{{ route('cabang.jamaah.index') }}"
       class="c-sidebar__link {{ request()->routeIs('cabang.jamaah.*') ? 'is-active' : '' }}">
        <i class="fas fa-users"></i>
        Jamaah
    </a>

    <a href="{{ route('cabang.agent.index') }}"
       class="c-sidebar__link {{ request()->routeIs('cabang.agent.*') ? 'is-active' : '' }}">
        <i class="fas fa-user-tie"></i>
        Agent
    </a>

</nav>
