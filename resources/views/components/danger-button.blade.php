{{-- Submit button; merges attributes and applies default red styling --}}

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 bg-neutral-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>