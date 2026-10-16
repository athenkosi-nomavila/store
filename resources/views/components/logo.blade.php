{{-- Brand mark: swap this file to rebrand. --}}
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex h-11 items-center gap-2 text-xl font-bold']) }} aria-label="{{ config('app.name') }} home">
    <svg class="text-brand-600" width="28" height="28" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
        <circle cx="16" cy="16" r="8" fill="currentColor"/>
        <g stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
            <path d="M16 2v3.5M16 26.5V30M2 16h3.5M26.5 16H30M6.1 6.1l2.5 2.5M23.4 23.4l2.5 2.5M6.1 25.9l2.5-2.5M23.4 8.6l2.5-2.5"/>
        </g>
    </svg>
    <span>{{ config('app.name') }}</span>
</a>
