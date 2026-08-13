{{-- HEADER / NAVBAR --}}
<header class="app-header">

    {{-- ================= LEFT ================= --}}
    <div class="header-left">

        {{-- TOGGLE SIDEBAR (MOBILE) --}}
        <button id="toggleSidebar"
                class="btn-icon d-lg-none"
                aria-label="Toggle Sidebar"
                type="button">
            <i class="fas fa-bars"></i>
        </button>

        {{-- LOGO --}}
        <div class="header-logo">
            <img src="{{ asset('hasantour.png') }}"
                 alt="Umrah Baik"
                 class="logo-img">
        </div>

        {{-- BRAND / TAGLINE --}}
        <div class="header-tagline">
            <div class="header-brand-title">Umrah Baik</div>
            <div class="header-brand-tagline">Satukan Keluarga</div>
        </div>

        {{-- OPTIONAL SUBTITLE --}}
        @isset($subtitle)
            <div class="header-subtitle d-none d-md-block">
                {{ $subtitle }}
            </div>
        @endisset

    </div>

    {{-- ================= RIGHT ================= --}}
    <div class="header-right">

        {{-- USER MENU --}}
        <div class="user-menu dropdown">

<button
    type="button"
    class="user-trigger dropdown-toggle"
    data-bs-toggle="dropdown"
    aria-expanded="false">


        <div class="avatar avatar-initial">
            {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 2)) }}
        </div>

        <span class="user-name d-none d-md-inline">
            {{ Auth::user()->nama }}
        </span>
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">

        <li class="px-3 py-2 text-center">
            <strong>{{ Auth::user()->nama }}</strong><br>
            <small class="text-muted">{{ Auth::user()->role }}</small>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a href="#"
               class="dropdown-item text-danger"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>

    </ul>

</div>

    </div>

</header>
