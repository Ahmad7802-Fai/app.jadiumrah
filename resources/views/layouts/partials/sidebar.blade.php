@php
    $menus = app(\App\Services\Navigation\SidebarBuilder::class)->build();
@endphp

<div 
    x-data="{ collapsed: false }"
    class="h-full flex flex-col bg-gradient-to-b from-primary-700 to-primary-900 text-white transition-all duration-300"
    :class="collapsed ? 'w-20' : 'w-64'"
>

    {{-- ================= BRAND HEADER ================= --}}
    <div class="flex items-center justify-between px-5 h-16 border-b border-white/10 shrink-0">

        <div class="flex items-center gap-3">

            {{-- LOGO --}}
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center font-bold">
                UC
            </div>

            <div x-show="!collapsed" x-transition>
                <div class="text-sm font-semibold leading-tight">
                    Umrah Core
                </div>
                <div class="text-[10px] text-white/50">
                    Travel Management
                </div>
            </div>

        </div>

        <button 
            @click="collapsed = !collapsed"
            class="hidden lg:flex text-white/60 hover:text-white transition"
        >
            ☰
        </button>

    </div>


    {{-- ================= NAVIGATION ================= --}}
    <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-6">

        @foreach($menus as $group)

            <div>

                {{-- SECTION TITLE --}}
                <div 
                    x-show="!collapsed"
                    class="text-[10px] text-white/40 uppercase tracking-widest px-3 mb-3"
                >
                    {{ $group['section'] }}
                </div>

                <div class="space-y-1">

                    @foreach($group['items'] as $item)

                        @php
                            $hasChildren = !empty($item['children']);
                            $isActive = $item['is_active'];
                        @endphp

                        <div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">

                            {{-- ================= SINGLE LINK ================= --}}
                            @if(!$hasChildren && $item['route'])

                                <a href="{{ route($item['route']) }}"
                                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                                   {{ $isActive 
                                        ? 'bg-white/15 shadow-inner'
                                        : 'hover:bg-white/10' }}">

                                    {{-- ACTIVE INDICATOR --}}
                                    <div class="w-1.5 h-1.5 rounded-full
                                        {{ $isActive ? 'bg-white' : 'bg-transparent group-hover:bg-white/50' }}">
                                    </div>

                                    <span x-show="!collapsed" class="text-sm truncate">
                                        {{ $item['label'] }}
                                    </span>

                                </a>

                            {{-- ================= PARENT ================= --}}
                            @else

                                <div
                                    @click="open = !open"
                                    class="group flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer transition
                                    {{ $isActive 
                                        ? 'bg-white/15 shadow-inner'
                                        : 'hover:bg-white/10' }}"
                                >

                                    <div class="flex items-center gap-3">

                                        <div class="w-1.5 h-1.5 rounded-full
                                            {{ $isActive ? 'bg-white' : 'bg-transparent group-hover:bg-white/50' }}">
                                        </div>

                                        <span x-show="!collapsed" class="text-sm truncate">
                                            {{ $item['label'] }}
                                        </span>

                                    </div>

                                    <svg 
                                        x-show="!collapsed"
                                        class="w-4 h-4 opacity-60 transition-transform duration-200"
                                        :class="open ? 'rotate-90' : ''"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 5l7 7-7 7" />
                                    </svg>

                                </div>

                                {{-- ================= CHILDREN ================= --}}
                                @if($hasChildren)

                                    <div 
                                        x-show="open"
                                        x-transition
                                        class="ml-6 mt-1 space-y-1"
                                    >

                                        @foreach($item['children'] as $child)

                                            @php
                                                $childActive = $child['is_active'];
                                            @endphp

                                            <a href="{{ route($child['route']) }}"
                                               class="flex items-center px-3 py-2 rounded-lg text-xs transition
                                               {{ $childActive 
                                                    ? 'bg-white/15'
                                                    : 'hover:bg-white/10' }}">
                                                {{ $child['label'] }}
                                            </a>

                                        @endforeach

                                    </div>

                                @endif

                            @endif

                        </div>

                    @endforeach

                </div>

            </div>

        @endforeach

    </nav>


    {{-- ================= FOOTER ================= --}}
    <div class="p-4 border-t border-white/10 text-[10px] text-white/40">
        <span x-show="!collapsed">
            © {{ date('Y') }} Umrah Core
        </span>
    </div>

</div>