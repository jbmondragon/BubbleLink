@props(['active' => false])

@php
    // Sidebar navigation link with active state styling
    $classes = $active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-neutral-900 text-base font-medium text-neutral-950 bg-neutral-100'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-neutral-700 hover:text-neutral-950 hover:bg-neutral-100 hover:border-neutral-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>