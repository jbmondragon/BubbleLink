@props(['active'])

@php
    // Navigation link with active state styling
    $classes = $active
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-neutral-900 text-sm font-medium text-neutral-950'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-neutral-700 hover:text-neutral-950 hover:border-neutral-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>