@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-teal-900']) }}>
    {{ $value ?? $slot }}
</label>
