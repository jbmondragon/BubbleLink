@props(['messages'])

{{-- Displays a list of error messages --}}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-neutral-700 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif