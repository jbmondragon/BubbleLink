@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-orange-400 text-start text-base font-medium text-teal-950 bg-orange-50/70 focus:outline-none focus:text-teal-950 focus:bg-orange-100/70 focus:border-orange-500 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-teal-800/80 hover:text-teal-950 hover:bg-white/70 hover:border-orange-200 focus:outline-none focus:text-teal-950 focus:bg-white/70 focus:border-orange-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
