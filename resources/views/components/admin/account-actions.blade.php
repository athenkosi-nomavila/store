@props(['stacked' => false])
{{-- "View store" and "Log out". Log out is wired up in the login step. --}}
<div @class(['flex gap-1', 'flex-col' => $stacked])>
    <a href="{{ route('home') }}" target="_blank" rel="noopener"
       class="flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium hover:bg-white/5 hover:text-white">
        <x-icon name="external" /><span @class(['sr-only' => ! $stacked])>View store</span>
    </a>
    <button type="button" disabled title="Available once login is added"
            class="flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-50">
        <x-icon name="log-out" /><span @class(['sr-only' => ! $stacked])>Log out</span>
    </button>
</div>
