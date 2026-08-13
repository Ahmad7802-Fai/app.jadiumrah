<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Tabungan Umrah')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ================= FONT ================= --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- ================= ICONS ================= --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    {{-- ================= STYLES ================= --}}
    @vite([
        'resources/js/app.js',
        'resources/scss/jamaah/jamaah.scss'
    ])

    @stack('styles')
</head>

<body>

{{-- ======================================================
| HEADER
====================================================== --}}
<header class="j-header">
    <a href="{{ route('jamaah.dashboard') }}" class="j-header__brand">
        <img
            src="{{ asset('hasantour.png') }}"
            alt="Tabungan Umrah"
            class="j-header__logo"
        >

        <div class="j-header__tagline">
            <span class="tagline-top">Umrah Baik</span>
            <span class="tagline-bottom">Satukan Keluarga</span>
        </div>
    </a>

    <a href="{{ route('jamaah.profile') }}"
       class="j-header__user"
       aria-label="Profile">
        <i class="fas fa-user-circle"></i>
    </a>
</header>


{{-- ======================================================
| MAIN CONTENT
====================================================== --}}
<main class="j-container" style="min-height: 100vh;">
    @yield('content')
</main>



{{-- ======================================================
| BOTTOM NAVIGATION
====================================================== --}}
<nav class="j-bottomnav">

    <a href="{{ route('jamaah.dashboard') }}"
       class="{{ request()->routeIs('jamaah.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('jamaah.tabungan.topup') }}"
       class="{{ request()->routeIs('jamaah.tabungan.topup') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i>
        <span>Top Up</span>
    </a>

    <a href="{{ route('jamaah.tabungan.index') }}"
       class="{{ request()->routeIs('jamaah.tabungan.index') ? 'active' : '' }}">
        <i class="fas fa-clock-rotate-left"></i>
        <span>Riwayat</span>
    </a>

    <a href="#"
       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <i class="fas fa-right-from-bracket"></i>
        <span>Logout</span>
    </a>

</nav>


{{-- ======================================================
| LOGOUT FORM (GUARD JAMAAH)
====================================================== --}}
<form id="logout-form"
      method="POST"
      action="{{ route('jamaah.logout') }}"
      class="d-none">
    @csrf
</form>


{{-- ======================================================
| FOOTER CREDIT (OPTIONAL)
====================================================== --}}
@if(config('app.show_credit', true))
    <footer class="j-footer-credit">
        Designed & engineered by
        <a href="https://ditelaga.id"
           target="_blank"
           rel="noopener noreferrer"
           class="j-footer-link">
            Ditelaga Creative Digital
        </a>
        <span class="j-footer-version">v1.0.0</span>
    </footer>
@endif


{{-- ================= SCRIPTS ================= --}}
@stack('scripts')

</body>
</html>
