<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Umrah Core') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="h-screen overflow-hidden bg-gray-100 text-gray-800 antialiased">

<div x-data="{ sidebarOpen: false }" class="h-full flex">

    {{-- ================= DESKTOP SIDEBAR ================= --}}
    <aside class="hidden lg:flex lg:flex-col w-64
                  bg-gradient-to-b from-primary-800 to-primary-900
                  text-white shadow-xl overflow-hidden">

        @include('layouts.partials.sidebar')

    </aside>


    {{-- ================= MOBILE SIDEBAR ================= --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64
               bg-gradient-to-b from-primary-800 to-primary-900
               text-white shadow-xl
               transform transition-transform duration-300
               lg:hidden overflow-hidden"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        @include('layouts.partials.sidebar')
    </aside>


    {{-- ================= OVERLAY (MOBILE) ================= --}}
    <div
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden"
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
    ></div>


    {{-- ================= CONTENT WRAPPER ================= --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        {{-- ================= HEADER ================= --}}
        <header class="h-16 shrink-0 bg-primary-800 text-white shadow-md flex items-center justify-between px-6 lg:px-8">

            {{-- MOBILE TOGGLE --}}
            <button
                class="lg:hidden text-white focus:outline-none"
                @click="sidebarOpen = true"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- PAGE TITLE --}}
            <div class="font-semibold text-sm tracking-wide truncate">
                @yield('page-title')
            </div>

            {{-- USER DROPDOWN --}}
            @include('layouts.partials.header')

        </header>


        {{-- ================= MAIN SCROLL AREA ================= --}}
        <main class="flex-1 overflow-y-auto bg-gray-100">

            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-8">

                {{-- FLASH SUCCESS --}}
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- FLASH ERROR --}}
                @if(session('error'))
                    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </div>

        </main>


        {{-- ================= FOOTER ================= --}}
        <footer class="shrink-0 bg-white border-t text-xs text-gray-500 px-6 py-3">
            © {{ date('Y') }} Umrah Core
        </footer>

    </div>

</div>

</body>
</html>