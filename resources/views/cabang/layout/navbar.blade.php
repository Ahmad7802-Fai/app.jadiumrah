<header class="app-header">

    {{-- LEFT --}}
    <div class="header-left">

        {{-- ☰ MOBILE TOGGLE --}}
        <button
            id="sidebarToggle"
            class="btn-icon-ghost d-md-none"
            aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <h1 class="header-title">
            @yield('page-title', 'Dashboard')
        </h1>
    </div>

    {{-- RIGHT --}}
    <div class="header-right">

        <div class="user-pill d-none d-sm-flex">
            <i class="fas fa-user-circle"></i>
            <span class="user-name">
                {{ auth()->user()->nama }}
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-icon-ghost" title="Logout">
                <i class="fas fa-arrow-right-from-bracket"></i>
            </button>
        </form>

    </div>

</header>
