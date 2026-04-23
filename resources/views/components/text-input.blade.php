@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-2xl border-orange-200 bg-white/90 text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600']) }}>
