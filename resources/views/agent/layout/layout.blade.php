<aside class="agent-sidebar d-flex flex-column">
    {{-- BRAND --}}
    <div class="sidebar-brand px-3 py-3 fw-semibold">
        Agent Panel
    </div>

    {{-- MENU --}}
    <nav class="nav flex-column px-2">
        <a href="{{ route('agent.dashboard') }}"
           class="nav-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('agent.leads.index') }}"
           class="nav-link {{ request()->routeIs('agent.leads.*') ? 'active' : '' }}">
            Lead
        </a>

        <a href="{{ route('agent.jamaah.index') }}"
           class="nav-link {{ request()->routeIs('agent.jamaah.*') ? 'active' : '' }}">
            Jamaah
        </a>
    </nav>

    {{-- FOOTER --}}
    <div class="mt-auto px-3 py-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light w-100 btn-sm">
                Logout
            </button>
        </form>
    </div>
</aside>
