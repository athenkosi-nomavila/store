@props(['title', 'icon' => 'alert', 'retry' => false])

@php
    $primary = 'inline-flex h-11 items-center justify-center gap-2 rounded-full bg-brand-600 px-6 font-semibold text-white shadow-sm hover:bg-brand-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600';
    $secondary = 'inline-flex h-11 items-center justify-center rounded-full border border-zinc-300 bg-white px-6 font-semibold text-zinc-700 hover:border-zinc-400 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600';
@endphp

<section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-24">
    <div class="mx-auto max-w-lg rounded-2xl border border-zinc-200 bg-white px-6 py-12 text-center shadow-sm sm:px-12">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 text-brand-600 ring-8 ring-brand-50/60">
            <x-icon :name="$icon" size="36" />
        </div>

        <h1 class="mt-8 text-balance text-3xl font-bold tracking-tight sm:text-4xl">{{ $title }}</h1>
        <p class="mx-auto mt-4 max-w-sm text-pretty text-zinc-500">{{ $slot }}</p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            @if ($retry)
                {{-- Only retry: when the server is failing, home fails too. --}}
                <a href="{{ request()->fullUrl() }}" class="{{ $primary }}"><x-icon name="refresh" size="18" /> Try again</a>
            @else
                <a href="{{ route('home') }}" class="{{ $primary }}">Back to home</a>
                <a href="{{ route('shop.index') }}" class="{{ $secondary }}">Browse products</a>
            @endif
        </div>
    </div>
</section>
