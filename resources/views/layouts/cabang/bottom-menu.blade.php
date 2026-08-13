<nav class="cabang-bottom-menu">
    <a href="{{ route('cabang.dashboard') }}"
       class="{{ request()->routeIs('cabang.dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>

    <a href="{{ route('cabang.leads.index') }}"
       class="{{ request()->routeIs('cabang.leads.*') ? 'active' : '' }}">
        <i class="fa-solid fa-bullseye"></i>
        <span>Leads</span>
    </a>

    <a href="{{ route('cabang.jamaah.index') }}"
       class="{{ request()->routeIs('cabang.jamaah.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i>
        <span>Jamaah</span>
    </a>

    <a href="{{ route('cabang.agent.index') }}"
       class="{{ request()->routeIs('cabang.agent.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user-tie"></i>
        <span>Agent</span>
    </a>
</nav>
