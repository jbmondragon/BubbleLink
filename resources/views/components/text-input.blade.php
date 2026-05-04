@props(['disabled' => false])

{{-- Text input with rounded styling and focus/disabled states --}}
<input @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'rounded-2xl border-neutral-300 bg-white/90 text-neutral-950 shadow-sm focus:border-neutral-900 focus:ring-neutral-900'
    ]) }}>