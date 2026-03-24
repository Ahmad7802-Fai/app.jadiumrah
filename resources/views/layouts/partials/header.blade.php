<div x-data="{ open: false }" class="relative">

    {{-- ================= BUTTON ================= --}}
    <button
        @click="open = !open"
        class="flex items-center gap-3 focus:outline-none group"
    >

        {{-- Avatar --}}
        <div class="w-9 h-9 rounded-full 
                    bg-white text-primary-800 
                    flex items-center justify-center 
                    font-semibold shadow-sm
                    group-hover:scale-105 
                    transition duration-200">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>

        {{-- Name + Role --}}
        <div class="hidden md:block text-left leading-tight">
            <div class="text-sm font-semibold text-white">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-white/70">
                {{ auth()->user()?->getRoleNames()->first() }}
            </div>
        </div>

        {{-- Arrow --}}
        <svg 
            class="w-4 h-4 text-white/80 transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"/>
        </svg>

    </button>


    {{-- ================= DROPDOWN ================= --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="open = false"
        x-cloak
        class="absolute right-0 mt-3 w-60 
               backdrop-blur-xl bg-white/90
               border border-white/40
               rounded-2xl shadow-2xl 
               overflow-hidden text-gray-700 z-50"
    >

        {{-- USER INFO --}}
        <div class="px-4 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
            <div class="font-semibold text-gray-800 text-sm">
                {{ auth()->user()->name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ auth()->user()->email }}
            </div>
        </div>

        {{-- MENU ITEMS --}}
        <div class="py-2">

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 text-sm
                      hover:bg-gray-100 transition rounded-lg mx-2">

                <span>Profile</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm
                           hover:bg-red-50 text-red-600 
                           transition rounded-lg mx-2">
                    Logout
                </button>
            </form>

        </div>

    </div>

</div>