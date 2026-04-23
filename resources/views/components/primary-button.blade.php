<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 rounded-full border border-transparent bg-teal-900 font-semibold text-xs uppercase tracking-[0.2em] text-amber-50 shadow-sm hover:bg-teal-800 focus:bg-teal-800 active:bg-teal-950 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
