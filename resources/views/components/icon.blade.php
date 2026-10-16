@props(['name', 'size' => 20])
{{-- Icons live in one sprite, public/images/icons.svg, so no markup is injected from PHP. --}}
<svg {{ $attributes->merge(['class' => 'icon']) }} width="{{ $size }}" height="{{ $size }}" aria-hidden="true" focusable="false"><use href="{{ asset('images/icons.svg') }}#{{ $name }}"></use></svg>
