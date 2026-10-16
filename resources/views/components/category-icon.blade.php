@props(['slug', 'size' => 28])
@php
    $icons = [
        'audio' => 'headphones',
        'wearables' => 'watch',
        'home-living' => 'lamp',
        'kitchen' => 'cup',
        'workspace' => 'laptop',
    ];
@endphp
<x-icon :name="$icons[$slug] ?? 'tag'" :size="$size" {{ $attributes }} />
