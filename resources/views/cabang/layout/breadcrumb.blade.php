@php
    /**
     * Format:
     * $breadcrumbs = [
     *   ['label' => 'Leads', 'url' => route('cabang.leads.index')],
     *   ['label' => 'Detail']
     * ];
     */
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

@if(!empty($breadcrumbs))
<nav class="cabang-breadcrumb">
    <ul>
        <li>
            <a href="{{ route('cabang.dashboard') }}">
                Dashboard
            </a>
        </li>

        @foreach($breadcrumbs as $item)
            <li>
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
@endif
