@props(['value'])

{{-- Form label with default styling --}}
<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-neutral-950']) }}>
    {{ $value ?? $slot }}
</label>