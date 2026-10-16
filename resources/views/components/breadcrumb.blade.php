@props(['links' => [], 'current'])
<nav aria-label="Breadcrumb" class="py-5 text-sm text-zinc-500">
    <ol class="flex flex-wrap gap-2">
        @foreach ($links as $label => $url)
            <li><a href="{{ $url }}" class="hover:text-brand-600">{{ $label }}</a> <span class="ml-2 text-zinc-300" aria-hidden="true">/</span></li>
        @endforeach
        <li aria-current="page" class="text-zinc-900">{{ $current }}</li>
    </ol>
</nav>
