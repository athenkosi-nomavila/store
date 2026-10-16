@props(['stacked' => false])
{{-- "View store" and "Log out". Logging out is a POST with a CSRF token, so another site can't log you out with a link. --}}
<div @class(['flex gap-1', 'flex-col' => $stacked])>
    <a href="{{ route('home') }}" target="_blank" rel="noopener"
       class="flex h-11 items-center gap-3 rounded-lg px-3 text-sm font-medium hover:bg-white/5 hover:text-white">
        <x-icon name="external" /><span @class(['sr-only' => ! $stacked])>View store</span>
    </a>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="flex h-11 w-full items-center gap-3 rounded-lg px-3 text-sm font-medium hover:bg-white/5 hover:text-white">
            <x-icon name="log-out" /><span @class(['sr-only' => ! $stacked])>Log out</span>
        </button>
    </form>
</div>
