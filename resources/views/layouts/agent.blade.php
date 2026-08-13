<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- GOOGLE FONT --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- FONT ICON --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        referrerpolicy="no-referrer"
    />
    {{-- APP CSS & JS --}}
    {{-- NOTE: app.js TIDAK BOLEH mengandung logic user menu --}}
    @vite([
        'resources/scss/agent.scss',
        'resources/js/app.js'
    ])

    <script src="{{ asset('assets/js/jamaah-form.js') }}"></script>

    @stack('styles')
</head>

<body data-app="agent">

<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        @include('agent.layout.sidebar')
    </aside>

    {{-- MAIN AREA --}}
    <div class="app-main">

        @include('agent.layout.header')

        <main class="page-content">

            @hasSection('page-title')
                <div class="page-header mb-4">
                    <div class="page-header-text">
                        <h2 class="page-title">@yield('page-title')</h2>

                        @hasSection('page-subtitle')
                            <p class="page-subtitle">@yield('page-subtitle')</p>
                        @endif
                    </div>

                    @hasSection('page-actions')
                        <div class="page-header-actions">
                            @yield('page-actions')
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')

        </main>

        @include('agent.layout.footer')
    </div>

</div>

@include('agent.layout.bottom-nav')

{{-- 🔥 INI KUNCI UTAMA --}}
@stack('modals')

@stack('scripts')

</body>
</html>
