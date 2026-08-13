<nav class="bg-white shadow px-4 py-3 flex justify-between items-center">
    <div class="font-semibold">
        Dashboard Jamaah
    </div>

    <div class="relative">
        <a href="{{ route('jamaah.tabungan.index') }}"
           class="text-gray-700">

            🔔

            @if(($jamaahUnreadNotifCount ?? 0) > 0)
                <span class="absolute -top-2 -right-2
                             bg-red-600 text-white text-xs
                             rounded-full px-1.5">
                    {{ $jamaahUnreadNotifCount }}
                </span>
            @endif
        </a>
    </div>
</nav>
