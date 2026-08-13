<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'JadiUmrah' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('iconjadiumrah.png') }}">
    <link rel="icon" href="{{ asset('iconjadiumrah.ico') }}">
    <link rel="shortcut icon" href="{{ asset('iconjadiumrah.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('iconjadiumrah.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('iconjadiumrah.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('iconjadiumrah.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ALPINE.JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body 
    class="bg-gray-100 antialiased"
    x-data="{
        mobileSidebar: false,
        collapse: false,

        init() {
            const DESKTOP_COLLAPSE_WIDTH = 1280;
            const MOBILE_BREAKPOINT = 1024;

            const update = () => {
                const w = window.innerWidth;

                // MOBILE (selalu full, tidak collapse)
                if (w <= MOBILE_BREAKPOINT) {
                    this.collapse = false;
                    return;
                }

                // DESKTOP mode
                this.collapse = w < DESKTOP_COLLAPSE_WIDTH;
            };

            update();
            window.addEventListener('resize', update);
        }
    }"
    x-init="init()"
>

    <!-- MOBILE OVERLAY -->
    <div 
        x-show="mobileSidebar"
        x-transition.opacity
        @click="mobileSidebar=false"
        class="fixed inset-0 bg-black/40 z-30 xl:hidden"
    ></div>


    <div class="flex">

        <!-- SIDEBAR -->
        @auth
            @include('layouts.sidebar')
        @endauth

        <!-- PAGE WRAPPER -->
        <div 
            class="flex-1 min-h-screen flex flex-col transition-all duration-300"
            :class="{
                'xl:ml-20': collapse && window.innerWidth > 1024,
                'xl:ml-64': !collapse && window.innerWidth > 1024
            }"
        >

            <!-- NAVBAR -->
            @auth
                @include('layouts.navbar')
            @endauth

            <!-- CONTENT -->
            <main class="flex-1 px-6 py-6">
                @yield('content')
            </main>

            <!-- FOOTER -->
            @auth
                @include('layouts.footer')
            @endauth

        </div>

    </div>

</body>
</html>
