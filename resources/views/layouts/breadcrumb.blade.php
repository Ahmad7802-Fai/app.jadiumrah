@php
    // Convert slug → Label rapi
    if (!function_exists('bc_label')) {
        function bc_label($text) {
            return ucwords(str_replace(['-', '_'], ' ', $text));
        }
    }

    $routeName = request()->route()->getName(); 
    $parts = explode('.', $routeName);

    $builtRoute = '';  // progressive route builder
@endphp

<ol class="breadcrumb bg-transparent p-0 mb-2">

    {{-- HOME --}}
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i>
        </a>
    </li>

    @foreach ($parts as $i => $p)

        @php
            // contoh: operator → operator.daftar-jamaah → operator.daftar-jamaah.index
            $builtRoute = $i === 0 ? $p : ($builtRoute . '.' . $p);

            // cek apakah route ini valid di Laravel
            $exists = Route::has($builtRoute);
        @endphp

        {{-- Jika bukan item terakhir = parent breadcrumb --}}
        @if ($i < count($parts) - 1)
            <li class="breadcrumb-item">
                @if ($exists)
                    <a href="{{ route($builtRoute) }}">{{ bc_label($p) }}</a>
                @else
                    <span class="text-muted">{{ bc_label($p) }}</span>
                @endif
            </li>

        {{-- Item terakhir = aktif --}}
        @else
            <li class="breadcrumb-item active" aria-current="page">
                {{ bc_label($p) }}
            </li>
        @endif
    @endforeach

</ol>
