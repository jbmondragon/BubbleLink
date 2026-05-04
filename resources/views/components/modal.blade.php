@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
@endphp

{{-- Reusable modal dialog with overlay and slot content --}}
<div x-data="modalDialog({ name: @js($name), show: @js($show) })"
     x-show="show"
     class="fixed inset-0 z-50"
     style="display: {{ $show ? 'block' : 'none' }}">

    <div x-show="show"
         x-on:click="close()"
         class="fixed inset-0 bg-black/50"></div>

    <div x-show="show"
         class="relative mx-auto mt-20 w-full {{ $maxWidth }} bg-white">
        {{ $slot }}
    </div>
</div>