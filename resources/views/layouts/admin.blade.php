<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ?? 'Dashboard') }} — JadiUmrah</title>

    {{-- FAVICON --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- GOOGLE FONT --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- FONT AWESOME --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    {{-- SELECT2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
          rel="stylesheet"/>

    {{-- JQUERY (WAJIB SEBELUM SELECT2) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- SELECT2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- APP (VITE) --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    {{-- CUSTOM PAGE STYLES --}}
    @stack('styles')
</head>

<body class="app">

    {{-- MOBILE OVERLAY --}}
    <div class="app-overlay" id="sidebarOverlay"></div>

    {{-- SIDEBAR --}}
    @include('layouts.sidebar.sidebar')

    {{-- MAIN WRAPPER --}}
    <div class="app-main">

        {{-- TOP NAVBAR --}}
        @include('layouts.navbar')

        {{-- ===============================
            MAIN CONTENT
        ================================ --}}
        <main class="app-content">

    @include('components.flash')
    @includeIf('layouts.breadcrumb')

    {{-- ===============================
       PAGE HEADER (GLOBAL SLOT)
    ================================ --}}
    @if(View::hasSection('page-title') || View::hasSection('page-actions'))
        <div class="page-header">

            <div>
                @hasSection('page-title')
                    <h4 class="page-title mb-1">
                        @yield('page-title')
                    </h4>
                @endif

                @hasSection('page-subtitle')
                    <small class="text-muted">
                        @yield('page-subtitle')
                    </small>
                @endif
            </div>

            @hasSection('page-actions')
                <div class="page-actions d-none d-md-flex gap-2">
                    @yield('page-actions')
                </div>
            @endif

        </div>
    @endif

    {{-- ===============================
       PAGE CONTENT
    ================================ --}}
    @yield('content')

</main>

        {{-- FOOTER --}}
        @include('layouts.footer')

    </div>

    {{-- LOGOUT FORM --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    {{-- BOOTSTRAP --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- FIX: INIT DROPDOWN HEADER --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document
            .querySelectorAll('.user-menu .dropdown-toggle')
            .forEach(function (el) {
                new bootstrap.Dropdown(el);
            });
    });
</script>

{{-- SIDEBAR TOGGLE --}}
<script>
    document.getElementById('toggleSidebar')?.addEventListener('click', () => {
        document.body.classList.toggle('is-sidebar-open');
    });

    document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
        document.body.classList.remove('is-sidebar-open');
    });
</script>

{{-- INIT SELECT2 --}}
<script>
    $(document).ready(function () {
        $('.select2').select2({ width: '100%' });
    });
</script>

@stack('scripts')
</body>
</html>
