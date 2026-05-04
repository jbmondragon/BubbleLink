@props([])

{{-- Owner navigation bar with active link styling --}}
<nav class="mb-8 flex flex-wrap items-center gap-3 rounded-xl border bg-white p-3 shadow-sm">

    @php
        // Styles active vs inactive navigation links
        $linkClasses = fn ($active) =>
            $active
                ? 'rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white'
                : 'rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200';
    @endphp

    <a href="{{ route('dashboard') }}" class="{{ $linkClasses(request()->routeIs('dashboard')) }}">Dashboard</a>
    <a href="{{ route('services.index') }}" class="{{ $linkClasses(request()->routeIs('services.*')) }}">Services</a>
    <a href="{{ route('orders.index') }}" class="{{ $linkClasses(request()->routeIs('orders.*')) }}">Orders</a>

</nav>