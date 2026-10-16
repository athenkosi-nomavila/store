{{-- Brand mark: swap this file to rebrand. --}}
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'logo']) }} aria-label="{{ config('app.name') }} home">
    <svg class="logo-mark" width="30" height="30" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
        <circle cx="16" cy="16" r="8" fill="currentColor"/>
        <g stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
            <path d="M16 2v3.5M16 26.5V30M2 16h3.5M26.5 16H30M6.1 6.1l2.5 2.5M23.4 23.4l2.5 2.5M6.1 25.9l2.5-2.5M23.4 8.6l2.5-2.5"/>
        </g>
    </svg>
    <span class="logo-text">Store</span>
</a>
