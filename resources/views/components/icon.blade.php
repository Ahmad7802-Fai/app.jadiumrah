@props([
    'name' => null,
    'class' => 'w-5 h-5'
])

@if(!$name)
    @return
@endif

@switch($name)

    {{-- Dashboard --}}
    @case('home')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M3 10l9-7 9 7v10a2 2 0 01-2 2h-4V12H9v10H5a2 2 0 01-2-2V10z"/>
        </svg>
    @break

    {{-- Users --}}
    @case('users')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M17 20h5V9H2v11h5m10 0v-2a4 4 0 00-8 0v2m8 0H9"/>
        </svg>
    @break

    {{-- Branches --}}
    @case('building-office')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M3 21h18M9 8h6m-6 4h6m-6 4h6M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16"/>
        </svg>
    @break

    {{-- Agents --}}
    @case('user-group')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M17 20h5V9H2v11h5m10 0v-2a4 4 0 00-8 0v2m8 0H9"/>
        </svg>
    @break

    {{-- Roles --}}
    @case('shield-check')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    @break

    {{-- Commission --}}
    @case('banknotes')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
    @break

    {{-- Schemes --}}
    @case('document-text')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M7 8h10M7 12h6m-6 4h10M5 4h14v16H5z"/>
        </svg>
    @break

    {{-- Config --}}
    @case('cog-6-tooth')
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                d="M9.75 3a.75.75 0 01.75-.75h3a.75.75 0 01.75.75v1.35a7.5 7.5 0 012.12.88l.96-.96a.75.75 0 011.06 0l2.12 2.12a.75.75 0 010 1.06l-.96.96c.38.67.68 1.39.88 2.12H21a.75.75 0 01.75.75v3a.75.75 0 01-.75.75h-1.35a7.5 7.5 0 01-.88 2.12l.96.96a.75.75 0 010 1.06l-2.12 2.12a.75.75 0 01-1.06 0l-.96-.96a7.5 7.5 0 01-2.12.88V21a.75.75 0 01-.75.75h-3A.75.75 0 019.75 21v-1.35a7.5 7.5 0 01-2.12-.88l-.96.96a.75.75 0 01-1.06 0L3.49 17.6a.75.75 0 010-1.06l.96-.96a7.5 7.5 0 01-.88-2.12H2a.75.75 0 01-.75-.75v-3A.75.75 0 012 9h1.35c.2-.73.5-1.45.88-2.12l-.96-.96a.75.75 0 010-1.06L5.39 2.74a.75.75 0 011.06 0l.96.96c.67-.38 1.39-.68 2.12-.88V3z"/>
        </svg>
    @break

    {{-- Default --}}
    @default
        <svg {{ $attributes->merge(['class' => $class]) }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
        </svg>

@endswitch