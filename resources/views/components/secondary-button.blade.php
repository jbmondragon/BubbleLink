{{-- Secondary button with neutral styling and states for hover, focus, and disabled --}}
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center px-4 py-2 rounded-full border border-neutral-300 bg-white/90 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-950 hover:bg-neutral-100 focus:ring-2 focus:ring-neutral-400 disabled:opacity-25 transition'
]) }}>
    {{ $slot }}
</button>