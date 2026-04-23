@props(['organization' => null, 'currentRole' => null])

<nav class="mb-8 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
    @php
        $linkClasses = static fn (bool $active): string => $active
            ? 'rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white'
            : 'rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-200';
    @endphp

    <a href="{{ route('dashboard') }}" class="{{ $linkClasses(request()->routeIs('dashboard')) }}">Dashboard</a>

    @if ($organization)
        @if ($currentRole === 'manager')
            <a href="{{ route('services.index') }}" class="{{ $linkClasses(request()->routeIs('services.*')) }}">Services</a>
        @endif

        @if (in_array($currentRole, ['manager', 'staff'], true))
            <a href="{{ route('orders.index') }}" class="{{ $linkClasses(request()->routeIs('orders.*')) }}">Orders</a>
        @endif

        @if ($currentRole === 'owner')
            <a href="{{ route('memberships.index') }}" class="{{ $linkClasses(request()->routeIs('memberships.*')) }}">Members</a>
        @endif
    @else
        <a href="{{ route('organizations.create') }}" class="{{ $linkClasses(request()->routeIs('organizations.create')) }}">Create Organization</a>
    @endif
</nav>