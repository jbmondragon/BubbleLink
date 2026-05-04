{{-- Button with primary styling and hover/focus states --}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 rounded-full bg-neutral-950 text-white text-xs font-semibold uppercase tracking-[0.2em] hover:bg-neutral-800 focus:ring-2 focus:ring-neutral-400 transition'
]) }}>
    {{ $slot }}
</button>