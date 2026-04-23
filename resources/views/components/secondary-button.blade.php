<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 rounded-full border border-orange-200 bg-white/90 font-semibold text-xs uppercase tracking-[0.2em] text-teal-900 shadow-sm hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
