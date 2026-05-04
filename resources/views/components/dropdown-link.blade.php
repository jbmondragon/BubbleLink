{{-- Link item; full-width clickable element with hover/focus styles --}}

<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-sm text-neutral-950 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>